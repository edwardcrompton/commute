<?php

// src/AppBundle/Controller/FeedController.php
namespace AppBundle\Controller;

use AppBundle\Manager\StationsFeedManager;
use AppBundle\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

//@todo: We should decouple this from StationsFeedManager.
class FeedController extends Controller
{
  const VAR_FEED_PAGE_NUMBER = 'feedcontroller_feed_page';
  const VAR_FEED_SWITCH = 'feedcontroller_feed_switch';
  const VAR_FEED_TIMESTAMP = 'feedcrontroller_feed_timestamp';
  // Period over which data refreshing runs in seconds.
  const DATA_REFRESH_PERIOD = 2592000;

  public function fetchAction()
  {
    // Use the Variable storage service to fetch the current page of results
    // we're on and whether a suitable time has elapsed since all results were
    // gathered.
    $storage = $this->get('app.storage');

    // Check here if VAR_FEED_SWITCH is 1 (on), or if it's 0 (off), check if a
    // suitable period of time has elapsed and then turn it on.
    if ($storage->getVar(self::VAR_FEED_SWITCH, 1) == 0) {
      // Check if a suitable amount of time has elapsed and turn the switch on if
      // so.
      $lastComplete = $storage->getVar(self::VAR_FEED_TIMESTAMP, 0);
      if (time() - $lastComplete < self::DATA_REFRESH_PERIOD) {
        // Do nothing, but return a useful message.
        return new Response(
          '<html><body>Stations were updated less than ' . self::DATA_REFRESH_PERIOD . ' ago.</body></html>'
        );
      }
      // Turn the update feed back on and continue.
      $storage->getVar(self::VAR_FEED_SWITCH, 1);
    }

    $page = $storage->getVar(self::VAR_FEED_PAGE_NUMBER, 1);

    $geoDetailsManager = new StationsFeedManager;
    $response = $geoDetailsManager->fetchStations($page);

    // Find out if we're on the last page.
    if ($page == ceil($response->total / $response->rpp)) {
      // Turn off the fetch for next time cron is run.
      $storage->setVar(self::VAR_FEED_SWITCH, 0);
      // Reset the page number that we'll fetch next time.
      $storage->setVar(self::VAR_FEED_PAGE_NUMBER, 1);
      // Set a timestamp to say when the data was last refreshed.
      $storage->setVar(self::VAR_FEED_TIMESTAMP, time());
    }
    else {
      // Update the number of the page we want to get for next time.
      $page = $response->page + 1;
    }

    // Fetch the doctrine manager for managing our persistent entities.
    $entityManager = $this->getDoctrine()->getManager();
    
    foreach ($response->stations as $station) {
      // See if this station already exists in the database.
      $location = $this->getDoctrine()
        ->getRepository('AppBundle:Station')
        ->findOneByCode($station->station_code);
      
      if (!$location) {
        // If it doesn't exist, create a new one.
        $location = new Station();
      }
      
      $location->setName($station->name);
      $location->setCode($station->station_code);
      $location->setLongitude($station->longitude);
      $location->setLatitude($station->latitude);

      $entityManager->persist($location);
    }
    
    $entityManager->flush();
    
    // Keep a count of the results page we're on.
    $storage->setVar(self::VAR_FEED_PAGE_NUMBER, $page);
    
    return new Response(
      '<html><body>Page ' . $page . ' of X stations fetched.</body></html>'
    );
  }
}
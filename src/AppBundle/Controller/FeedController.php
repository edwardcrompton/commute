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

  public function __construct()
  {
    // Assume that the current page we're going to fetch is zero.
    $this->page = 0;
  }

  public function fetchAction()
  {
    $this->storage = $this->get('app.storage');

    if (!$this->isFeedActive()) {
      // Do nothing, but return a useful message.
      return new Response(
        '<html><body>Stations were updated less than ' . self::DATA_REFRESH_PERIOD . ' ago.</body></html>'
      );
    }

    $this->page = $this->storage->getVar(self::VAR_FEED_PAGE_NUMBER, 1);

    $geoDetailsManager = new StationsFeedManager;
    $this->response = $geoDetailsManager->fetchStations($this->page);

    // Check if this is the final page and set relevant vars if so.
    $this->finalPageActions();

    // Fetch the doctrine manager for managing our persistent entities.
    $entityManager = $this->getDoctrine()->getManager();
    
    foreach ($this->response->stations as $station) {
      // Persist each station in the database.
      $this->persistStation($station, $entityManager);
    }
    
    $entityManager->flush();
    
    // Keep a count of the results page we're on.
    $this->storage->setVar(self::VAR_FEED_PAGE_NUMBER, $this->page);
    
    return new Response(
      '<html><body>Page ' . $this->page . ' of X stations fetched.</body></html>'
    );
  }

  /**
   * Checks to see if this is the final page of stations that can be fetched for
   * this query, resets counters and sets a timestamp if so.
   */
  private function finalPageActions() {
    // Find out if we're on the last page.
    if ($this->page == ceil($this->response->total / $this->response->rpp)) {
      // Turn off the fetch for next time cron is run.
      $this->storage->setVar(self::VAR_FEED_SWITCH, 0);
      // Reset the page number that we'll fetch next time.
      $this->storage->setVar(self::VAR_FEED_PAGE_NUMBER, 1);
      // Set a timestamp to say when the data was last refreshed.
      $this->storage->setVar(self::VAR_FEED_TIMESTAMP, time());
    }
    else {
      // Update the number of the page we want to get for next time.
      $this->page = $this->response->page + 1;
    }
  }

  /**
   * Checks to see if the feed is currently active, and makes it active if a
   * suitable amount of time has elapsed since it last ran.
   *
   * @return bool
   */
  private function isFeedActive() {
    // Check here if VAR_FEED_SWITCH is 1 (on), or if it's 0 (off), check if a
    // suitable period of time has elapsed and then turn it on.
    if ($this->storage->getVar(self::VAR_FEED_SWITCH, 1) == 0) {
      // Check if a suitable amount of time has elapsed and turn the switch on if
      // so.
      $lastComplete = $this->storage->getVar(self::VAR_FEED_TIMESTAMP, 0);
      if (time() - $lastComplete < self::DATA_REFRESH_PERIOD) {
        return false;
      }
      // Turn the update feed back on and continue.
      $this->storage->getVar(self::VAR_FEED_SWITCH, 1);
    }
    return true;
  }

  /**
   * @param $station
   * @param $entityManager
   */
  private function persistStation($station, $entityManager) {
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
}
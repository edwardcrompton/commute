<?php

// src/AppBundle/Controller/FeedController.php
namespace AppBundle\Controller;

use AppBundle\Manager\StationsFeedManager;
use AppBundle\Controller\VariableController;
use AppBundle\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

//@todo: We should rename this and decouple it from StationsFeedManager.
class FeedController extends Controller
{
  const VAR_FEED_PAGE_NUMBER = 'feedcontroller_feed_page';

  public function fetchAction()
  {
    // Use the Variable storage service to fetch the current page of results
    // we're on.
    $storage = $this->get('app.storage');
    $page = $storage->getVar(self::VAR_FEED_PAGE_NUMBER, 0);

    $geoDetailsManager = new StationsFeedManager;
    $response = $geoDetailsManager->fetchStations($page);
    
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
    $variableController = new VariableController;
    $variableController->setVar(self::VAR_FEED_PAGE_NUMBER, $page);
    
    return new Response(
      '<html><body>Page ' . $page . ' of X stations fetched.</body></html>'
    );
  }
}
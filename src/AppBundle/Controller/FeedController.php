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
  public function fetchAction()
  {
    $page = 1; // Fetch from variables.
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
    $variableController->setVar('feed_page', $page);
    
    return new Response(
      '<html><body>Stations fetched.</body></html>'
    );
  }
}
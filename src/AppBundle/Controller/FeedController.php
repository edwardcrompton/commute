<?php

// src/AppBundle/Controller/FeedController.php
namespace AppBundle\Controller;

use AppBundle\Manager\StationsFeedManager;
use AppBundle\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends Controller
{
  public function fetchAction()
  {
    $geoDetailsManager = new StationsFeedManager;
    $response = $geoDetailsManager->fetchStations();
    
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
    
    return new Response(
      '<html><body>Stations fetched.</body></html>'
    );
  }
}
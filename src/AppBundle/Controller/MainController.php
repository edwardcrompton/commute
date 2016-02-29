<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Manager\StationsDataManager;

class MainController extends Controller
{
  /**
   * @Route("/map", name="map")
   */
  public function renderAction(Request $request)
  {
    $stations = $this->loadStations();
    return $this->render('basemap.html.twig',
      array('stations' => $stations)
    );
  }

  /**
   * Gets a json encoded list of stations from the database.
   * 
   * @return string
   */
  public function loadStations() {
    $stations = $this->getDoctrine()
      ->getRepository('AppBundle:Station')
      ->findAll();
    
    $stationData = array();
    // We need to unpack the station objects into an array because a lot of 
    // their properties are protected.
    foreach ($stations as $station) {
      $stationData[$station->getCode()] = array(
        'name' => $station->getName(),
        'latitude' => $station->getLatitude(),
        'longitude' => $station->getLongitude(),
      );
    }
    return json_encode($stationData);
  }
}

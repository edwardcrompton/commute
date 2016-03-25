<?php

namespace AppBundle\Manager;

/**
 * @file
 *  Contains the StationsManager class.
 */

/**
 * Handles actions to do with handling stations in the database.
 */
class StationsManager {
  
  protected $entityManager;

  // Dependency injection in action. In services.yml we specify that the
  // doctrine entity manager class should be passed in here.
  public function __construct($entityManager)
  {
    $this->entityManager = $entityManager; //$entityManager->getDoctrine()->getManager();
  }
  
  public function loadStationObjects() {
    $stations = $this->entityManager
      ->getRepository('AppBundle:Station')
      ->findAll();
    return $stations;
  }
  
  /**
   * Gets an array of stations from the database.
   * 
   * @return string
   */
  public function loadStationArray() {
    $stations = $this->loadStationObjects();
    
    $stationData = array();
    // We need to unpack the station objects into an array because a lot of 
    // their properties are protected.
    foreach ($stations as $station) {
      $stationData[$station->getCode()] = array(
        'id' => $station->getId(),
        'name' => $station->getName(),
        'code' => $station->getCode(),
        'latitude' => $station->getLatitude(),
        'longitude' => $station->getLongitude(),
      );
    }
    return $stationData;
  }
  
  /**
   * Gets a json encoded list of stations from the database.
   * 
   * @return string
   */
  public function getJsonStations() {
    $stations = $this->loadStationArray();
    return json_encode($stations);
  }
}

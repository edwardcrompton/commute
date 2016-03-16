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
  
  /**
   * Gets an array of stations from the database.
   * 
   * @return string
   */
  public function loadStations() {
    $stations = $this->entityManager
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
    return $stationData;
  }
  
  /**
   * Gets a json encoded list of stations from the database.
   * 
   * @return string
   */
  public function getJsonStations() {
    $stations = $this->loadStations();
    return json_encode($stations);
  }
}

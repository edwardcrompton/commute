<?php
/**
 * @file
 *  Contains the RouteFeedManager class.
 */

namespace AppBundle\Manager;

/**
 * Class to manage the route data feed.
 */
class RouteFeedManager {
  
  /**
   * Constructor to set dependencies.
   * 
   * @param type $stationsManager
   */
  public function __construct($stationsManager) {
    $this->stationsManager = $stationsManager;
  }
  
  public function fetchRoutes() {
    $stations = $this->stationsManager->loadStations();
    //@todo: We need to loop through the stations here create routes from each.
  }
  
}

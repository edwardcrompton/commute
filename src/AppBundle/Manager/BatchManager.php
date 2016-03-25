<?php
/**
 * @file
 *  Contains the FeedManager class.
 */

namespace AppBundle\Manager;

/**
 * Handles periodic tasks that involve fetching data from the feed.
 */
class BatchManager {
  /**
   * Constructor.
   * 
   * @param type $feedManager
   */
  public function __construct($stationsFeedManager, $routeFeedManager) {
    $this->stationsFeedManager = $stationsFeedManager;
    $this->routeFeedManager = $routeFeedManager;
  }
  
  /**
   * The main routing function for the FeedController route.
   */
  public function feedController() {
    
    if ($this->stationsFeedManager->wasError() || $this->stationsFeedManager->wasProductive()) {
      $response = $this->stationsFeedManager->feedController();
      return $response;
    }
    else if (!$this->routeFeedManager->areServicesFetched()) {
      // The stations have been fetched correctly, but we haven't fetched the 
      // rail services yet.
      $servicesResponse = $this->routeFeedManager->fetchServiceIds();
      return $servicesResponse;
    }
    else if (!$this->routeFeedManager->areServiceDetailsFetched()) {
      // We're now ready to fetch the route details themselves.
      $routesResponse = $this->routeFeedManager->fetchRouteDetails();
      return $routesResponse;
    }
    else {
      // Do something default for when all details have been fetched.
    }
  }
}

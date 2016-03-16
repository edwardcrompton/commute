<?php
/**
 * @file
 *  Contains the FeedManager class.
 */

namespace AppBundle\Manager;

/**
 * Handles periodic tasks that involve fetching data from the feed.
 */
class FeedManager {
  /**
   * Constructor.
   * 
   * @param type $feedManager
   */
  public function __construct($stationsFeedManager) {
    $this->stationsFeedManager = $stationsFeedManager;
  }
  
  /**
   * The main routing function for the FeedController route.
   */
  public function feedController() {
    $response = $this->stationsFeedManager->feedController();
    if ($this->stationsFeedManager->wasError() || $this->stationsFeedManager->wasProductive()) {
      return $response;
    }
    else {
      // We'll do something else because we're not fetching stations at the
      // current point in time.
      return \Symfony\Component\HttpFoundation\Response::
    }
  }
  
  
}

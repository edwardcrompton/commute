<?php
/**
 * @file
 *  Contains the RouteFeedManager class.
 */

namespace AppBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class to manage the route data feed.
 */
class RouteFeedManager {
  
  /**
   * Constructor to set dependencies.
   * 
   * @param type $stationsManager
   */
  public function __construct($stationsManager, $settingsManager, $curl) {
    $this->stationsManager = $stationsManager;
    $this->settingsManager = $settingsManager;
    $this->curl = $curl;
    $this->services = array();
  }
  
  /**
   * 
   * @return Response
   */
  public function fetchRoutes() {
    $stationCodes = $this->settingsManager->values['station.origins'];
    
    foreach ($stationCodes as $code) {
      $routes = $this->getRouteForStation($code, '2016-05-09', '06:00');
      
      if (!isset($routes->departures)) {
        // Log an error. Perhaps the date is in the past, there are no trains,
        // or there was a curl error.
      }
      
      foreach ($routes->departures->all as $departure) {
        // Maintain a list of unique services.
        $this->services[$departure->service] = $departure->service;
      }
    }
    
    return new Response(
      '<html><body>Fetching routes between stations.</body></html>'
    );
  }
  
  /**
   * 
   */
  public function getRouteForStation($code, $date, $time) {
    // We can add an error handling function like this, but not sure how to
    // use a method on a object.
    // $this->curl->errorFunction =
    $url = $this->settingsManager->getTimeTableUrl($code, $date, $time);
    
    $requestVars = array(
      'app_id' => $this->settingsManager->getAppId(),
      'app_key' => $this->settingsManager->getAppKey(),
      'train_status' => 'passenger',
    );

    return $this->curl->get($url, $requestVars);
  }
  
  /**
   * 
   */
  public function getService($service, $date, $time) {
    // We can add an error handling function like this, but not sure how to
    // use a method on a object.
    // $this->curl->errorFunction =
    $url = $this->settingsManager->getServiceUrl($service, $date, $time);
    
    $requestVars = array(
      'app_id' => $this->settingsManager->getAppId(),
      'app_key' => $this->settingsManager->getAppKey(),
    );

    return $this->curl->get($url, $requestVars);
  }
}

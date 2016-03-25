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
  // The variable to store the serialised services array.
  const VAR_FEED_SERVICES = 'routefeedmanager_services';
  // The variable to store the number of services fetched so far.
  const VAR_FEED_SERVICES_FETCHED = 'routefeedmanager_service_fetched';
  
  // The base feed manager class.
  protected $baseFeedManager;
  
  // The flag to show that service ids have been fetched.
  protected $servicesFetched = false;
  
  // The flag to show that all the service details have been fetched.
  protected $serviceDetailsFetched = false;

  // The date and time that we'll use to fetch the route / service data.
  protected $date = '2016-05-09';
  protected $time = '06:00';


  /**
   * Constructor to set dependencies.
   * 
   * @param type $stationsManager
   */
  public function __construct($baseFeedManager, $stationsManager) {
    $this->baseServices = $baseFeedManager;
    $this->stationsManager = $stationsManager;
    $this->services = array();
  }
  
  /**
   * 
   * @return Response
   */
  public function fetchServiceIds() {
    $stationCodes = $this->baseServices->settingsManager->values['station.origins'];
    
    foreach ($stationCodes as $code) {
      $routes = $this->getServiceIdsForStation($code, $this->date, $this->time);
      
      if (!isset($routes->departures)) {
        // Log an error. Perhaps the date is in the past, there are no trains,
        // or there was a curl error.
      }
      
      foreach ($routes->departures->all as $departure) {
        // Maintain a list of unique services.
        $this->services[$departure->service] = $departure->service;
      }
    }
    
    // Write the services to the database so we can fetch them back on the
    // next batch run.
    $this->baseServices->storageManager->setVar(self::VAR_FEED_SERVICES, serialize($this->services));
    
    // If the stations were fetched successfully, set a persistent variable to
    // say so.
    $this->servicesFetched = true;
    
    return new Response(
      '<html><body>Fetched routes between key stations.</body></html>'
    );
  }
  
  /**
   * Given a station code, date and time, fetches the services running from
   * that station.
   * 
   * @param string $code
   *  The departure station code.
   * @param sting $date
   *  The departure date.
   * @param string $time 
   *  The departure time.
   * 
   * @return object
   *  Response object.
   */
  public function getServiceIdsForStation($code, $date, $time) {
    // We can add an error handling function like this, but not sure how to
    // use a method on a object.
    // $this->curl->errorFunction =
    $url = $this->baseServices->settingsManager->getTimeTableUrl($code, $date, $time);
    
    $requestVars = array(
      'app_id' => $this->baseServices->settingsManager->getAppId(),
      'app_key' => $this->baseServices->settingsManager->getAppKey(),
      'train_status' => 'passenger',
    );

    return $this->baseServices->requestManager->get($url, $requestVars);
  }
  
  /**
   * Returns true if the services have already been fetched, false otherwise.
   * 
   * @return bool
   */
  public function areServicesFetched() {
    return $this->servicesFetched;
  }
  
  /**
   * 
   */
  public function areServiceDetailsFetched() {
    return $this->serviceDetailsFetched;
  }
  
  
  /**
   * 
   */
  public function fetchRouteDetails() {
    
    $services = unserialize($this->storageManager->getVar(self::VAR_FEED_SERVICES));
    $servicesProcessed = $this->storageManager->getVar(self::VAR_FEED_SERVICES_FETCHED, 0);
    
    // We can add an error handling function like this, but not sure how to
    // use a method on a object.
    // $this->curl->errorFunction =
    $requestVars = array(
      'app_id' => $this->settingsManager->getAppId(),
      'app_key' => $this->settingsManager->getAppKey(),
    );

    $service = $services[$servicesProcessed];
    $url = $this->settingsManager->getServiceUrl($service, $this->date, $this->time);
    $this->curl->get($url, $requestVars);
    // We don't really know what the response is going to look like yet.

    $servicesProcessed++;
    
    if ($servicesProcessed == count($services)) {
      $servicesProcessed = 0;
      $this->serviceDetailsFetched = true;
    }
    $this->storageManager->setVar(self::VAR_FEED_SERVICES_FETCHED, $servicesProcessed);
    
    return; //Something.
  }
}

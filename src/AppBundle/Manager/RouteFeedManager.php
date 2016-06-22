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
  // The variable to store the number of service ids fetched so far.
  const VAR_FEED_COUNT_SERVICE_DETAILS_FETCHED = 'routefeedmanager_service_fetched';
  // The variable to store a flag as to whether all the service ids have been fetched.
  const VAR_FEED_BOOL_SERVICE_IDS_COMPLETE = 'routefeedmanager_service_ids_complete';
  // The variable to store a flag as to whether all the service details have been fetched.  
  const VAR_FEED_BOOL_SERVICES_COMPLETE = 'routefeedmanager_service_complete';
  
  // The base feed manager class.
  protected $baseFeedManager;
  
  // The flag to show that service ids have been fetched.
  //protected $servicesFetched = false;
  
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
    $this->baseServices->storageManager->setVar(self::VAR_FEED_SERVICES, $this->services);
    
    // If the service ids were fetched successfully, set a persistent variable to
    // say so.
    $this->baseServices->storageManager->setVar(self::VAR_FEED_BOOL_SERVICE_IDS_COMPLETE, true);
    
    return new Response(
      '<html><body>Fetched service IDs for key stations.</body></html>'
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
  public function areServiceIdsFetched() {
    return $this->baseServices->storageManager->getVar(self::VAR_FEED_BOOL_SERVICE_IDS_COMPLETE, false);
  }
  
  /**
   * 
   */
  public function areServiceDetailsFetched() {
    return $this->serviceDetailsFetched;
  }
  
  
  /**
   * Given that the service ids have been fetched, retreive the journey details
   * from them.
   */
  public function fetchRouteDetails() {
    $services = array_keys($this->baseServices->storageManager->getVar(self::VAR_FEED_SERVICES));
    $servicesProcessed = $this->baseServices->storageManager->getVar(self::VAR_FEED_COUNT_SERVICE_DETAILS_FETCHED, 0);
    
    // We can add an error handling function like this, but not sure how to
    // use a method on a object.
    // $this->curl->errorFunction =
    $requestVars = array(
      'app_id' => $this->baseServices->settingsManager->getAppId(),
      'app_key' => $this->baseServices->settingsManager->getAppKey(),
    );

    $service = $services[$servicesProcessed];
    $url = $this->baseServices->settingsManager->getServiceUrl($service, $this->date, $this->time);
    $serviceDetails = $this->baseServices->requestManager->get($url, $requestVars);
    // We don't really know what the response is going to look like yet.
    // We probably want to call a separate method here to process the service 
    // details and store them in the database.
    
    $this->saveServiceDetails($serviceDetails);
    
    $servicesProcessed++;
    
    if ($servicesProcessed == count($services)) {
      // If we've finished, reset the services counter for next time.
      $servicesProcessed = 0;
      // Set a flag to show that all the service details have been fetched.
      $this->baseServices->storageManager->setVar(self::VAR_FEED_BOOL_SERVICES_COMPLETE, true);
    }
    $this->baseServices->storageManager->setVar(self::VAR_FEED_COUNT_SERVICE_DETAILS_FETCHED, $servicesProcessed);
    
    return new Response(
      '<html><body>Fetched service details for key stations.</body></html>'
    );
  }
  
  /**
   * Writes the details of a service to the database.
   * 
   * @param type $serviceDetails
   */
  protected function saveServiceDetails($serviceDetails) {
    // Create this route in the database first.
    
    
    foreach ($serviceDetails->stops as $stop) {
      
    }
  }
  
  /**
   * Saves a station entity to the database if it doesn't already exist.
   *
   * @param $stop
   */
  private function persistStop($stop) {
    // This is very similar to persistStation in the StationsFeedManager.
    // See if this station already exists in the database.
    $location = $this->baseServices->entityManager->getRepository('AppBundle:Station')
      ->findOneByCode($stop->station_code);

    if (!$location) {
      // If it doesn't exist, don't bother?.
      return;
    }

    $location->setRoute($stop->station_code);
    
    $this->baseServices->entityManager->persist($location);
  }
  
  /**
   * 
   * @param type $service
   */
  private function persistService($service) {
    $numberOfStops = count($service->stops);
    $origin = $service->stops[0]->station_code;
    $destination = $service->stops[$numberOfStops - 1]->station_code;

    //$location = $this->baseServices->entityManager->getRepository('AppBundle:Route')
    //  ->findOneByCode($stop->station_code);
    // Need to get the service by combo of:
    //  $service->stops[0]->station_code
    //  $service->stops[$count]->station_code
    
    // For now we'll just assume each service is unique.
    $service = new \AppBundle\Entity\Route;
    $service->setOriginId($origin);
    $service->setDestinationId($destination);
    
    $this->baseServices->entityManager->persist($service);
  }
  
}

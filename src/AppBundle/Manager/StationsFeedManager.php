<?php
/**
 * @file
 *  Contains the StationsFeedManager class.
 */

namespace AppBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Station;

/**
 * Class to manage the station data feed.
 */
class StationsFeedManager {

  // The API URL.
  const TRANSPORT_API_URL = 'http://transportapi.com/v3/uk/train/stations/bbox.json';
  // The name of the persistent var to store the current feed page in.
  const VAR_FEED_PAGE_NUMBER = 'feedcontroller_feed_page';
  // The name of the persistent var to store the feed on / off flag in.
  const VAR_FEED_SWITCH = 'feedcontroller_feed_switch';
  // The name of the persistent var to store the feed timestamp in.
  const VAR_FEED_TIMESTAMP = 'feedcrontroller_feed_timestamp';
  // Period over which data refreshing runs in seconds.
  const DATA_REFRESH_PERIOD = 2592000;

  // Services that will be injected into this class.
  protected $storage;
  protected $entityManager;
  protected $curl;

  /**
   * Set up the services and vars we need to fetch from the stations feed.
   */
  public function __construct($entityManager, $storage, $curl, $settingsManager) {
    // Service for handling persistent variable storage.
    $this->storage = $storage;
    // Service for managing persistent entity storage.
    $this->entityManager = $entityManager;
    // Curl request service.
    $this->curl = $curl;
    // Service for handling app settings.
    $this->settingsManager = $settingsManager;
    
    // Period in seconds for fetching new station data.
    $this->dataRefreshPeriod = $this->settingsManager->getFetchPeriod();
    
    $this->maxlat = '53.6';
    $this->maxlon = '-1.5';
    $this->minlat = '51.0';
    $this->minlon = '-5.5';

    // Assume that the current page we're going to fetch is zero until we
    // discover otherwise.
    $this->page = 0;
  }
  
  /**
   * Issues a curl request to the api for stations we require.
   * 
   * @return null|curl response
   */
  public function fetchStations($page = 1) {
    $requestVars = array(
      'app_id' => $this->settingsManager->getAppId(),
      'app_key' => $this->settingsManager->getAppKey(),
      'maxlat' => $this->maxlat,
      'maxlon' => $this->maxlon,
      'minlat' => $this->minlat,
      'minlon' => $this->minlon,
      'page' => $page,
    );

    // We can add an error handling function like this, but not sure how to
    // use a method on a object.
    // $this->curl->errorFunction = 
    $this->curl->get($this->settingsManager->getStationUrl(), $requestVars);
    
    return $this->curl;
  }

  /**
   * Triggers the feed API request and returns an HTML response.
   *
   * @return \AppBundle\Manager\Response
   */
  public function feedController()
  {
    if (!$this->isFeedActive()) {
      // Do nothing, but return a useful message.
      return new Response(
        '<html><body>Stations were updated less than ' . $this->dataRefreshPeriod . ' seconds ago.</body></html>'
      );
    }

    $this->page = $this->storage->getVar(self::VAR_FEED_PAGE_NUMBER, 1);

    $this->curlResponse = $this->fetchStations($this->page);
    
    if ($this->curlResponse->response == NULL) {
      // Do nothing, but return a useful message.
      return new Response(
        '<html><body>Could not obtain a response from the server. Response error code: ' . $this->curlResponse->errorCode . '</body></html>'
      );
    }

    // Check if this is the final page and set relevant vars if so.
    $this->finalPageActions();

    foreach ($this->curlResponse->response->stations as $station) {
      // Persist each station in the database.
      $this->persistStation($station);
    }

    $this->entityManager->flush();

    // Keep a count of the results page we're on.
    $this->storage->setVar(self::VAR_FEED_PAGE_NUMBER, $this->page);

    return new Response(
      '<html><body>Page ' . $this->page . ' of X stations fetched.</body></html>'
    );
  }

  /**
   * Checks to see if this is the final page of stations that can be fetched for
   * this query, resets counters and sets a timestamp if so.
   */
  private function finalPageActions() {
    // Find out if we're on the last page.
    if ($this->page == ceil($this->curlResponse->response->total / $this->curlResponse->response->rpp)) {
      // Turn off the fetch for next time cron is run.
      $this->storage->setVar(self::VAR_FEED_SWITCH, 0);
      // Reset the page number that we'll fetch next time.
      $this->storage->setVar(self::VAR_FEED_PAGE_NUMBER, 1);
      // Set a timestamp to say when the data was last refreshed.
      $this->storage->setVar(self::VAR_FEED_TIMESTAMP, time());
    }
    else {
      // Update the number of the page we want to get for next time.
      $this->page = $this->curlResponse->response->page + 1;
    }
  }

  /**
   * Checks to see if the feed is currently active, and makes it active if a
   * suitable amount of time has elapsed since it last ran.
   *
   * @return bool
   */
  private function isFeedActive() {
    // Check here if VAR_FEED_SWITCH is 1 (on), or if it's 0 (off), check if a
    // suitable period of time has elapsed and then turn it on.
    if ($this->storage->getVar(self::VAR_FEED_SWITCH, 1) == 0) {
      // Check if a suitable amount of time has elapsed and turn the switch on if
      // so.
      $lastComplete = $this->storage->getVar(self::VAR_FEED_TIMESTAMP, 0);
      if (time() - $lastComplete < $this->dataRefreshPeriod) {
        return false;
      }
      // Turn the update feed back on and continue.
      $this->storage->getVar(self::VAR_FEED_SWITCH, 1);
    }
    return true;
  }

  /**
   * Saves a station entity to the database if it doesn't already exist.
   *
   * @param $station
   */
  private function persistStation($station) {
    // See if this station already exists in the database.
    $location = $this->entityManager->getRepository('AppBundle:Station')
      ->findOneByCode($station->station_code);

    if (!$location) {
      // If it doesn't exist, create a new one.
      $location = new Station();
    }

    $location->setName($station->name);
    $location->setCode($station->station_code);
    $location->setLongitude($station->longitude);
    $location->setLatitude($station->latitude);

    $this->entityManager->persist($location);
  }
}

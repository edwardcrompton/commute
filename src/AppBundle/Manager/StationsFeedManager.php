<?php
/**
 * @file
 *  Contains the GeoDetailsManager class.
 */

namespace AppBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Station;
use \Curl\Curl;

/**
 * Description of GeoDetailsManager
 *
 * @author edward
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
   *
   */
  public function __construct($entityManager, $storage, $curl) {
    $this->storage = $storage;
    $this->entityManager = $entityManager;
    $this->curl = $curl;

    // These should be taken out and put in a config file.
    $this->app_id = '98b1d17e';
    $this->app_key = 'f4f457e81bb4f207fdfe0e418d5eec6f';
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
      'app_id' => $this->app_id,
      'app_key' => $this->app_key,
      'maxlat' => $this->maxlat,
      'maxlon' => $this->maxlon,
      'minlat' => $this->minlat,
      'minlon' => $this->minlon,
      'page' => $page,
    );

    $this->curl->get(self::TRANSPORT_API_URL, $requestVars);

    if ($this->curl->error) {
      // @todo: Do some more intelligent logging here instead of an echo.
      // Also there are errors that we get that aren't curl errors.
      echo 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage;
      return NULL;
    }
    else {
      return $this->curl->response;
    }
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
        '<html><body>Stations were updated less than ' . self::DATA_REFRESH_PERIOD . ' ago.</body></html>'
      );
    }

    $this->page = $this->storage->getVar(self::VAR_FEED_PAGE_NUMBER, 1);

    $this->response = $this->fetchStations($this->page);

    // Check if this is the final page and set relevant vars if so.
    $this->finalPageActions();

    foreach ($this->response->stations as $station) {
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
    if ($this->page == ceil($this->response->total / $this->response->rpp)) {
      // Turn off the fetch for next time cron is run.
      $this->storage->setVar(self::VAR_FEED_SWITCH, 0);
      // Reset the page number that we'll fetch next time.
      $this->storage->setVar(self::VAR_FEED_PAGE_NUMBER, 1);
      // Set a timestamp to say when the data was last refreshed.
      $this->storage->setVar(self::VAR_FEED_TIMESTAMP, time());
    }
    else {
      // Update the number of the page we want to get for next time.
      $this->page = $this->response->page + 1;
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
      if (time() - $lastComplete < self::DATA_REFRESH_PERIOD) {
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

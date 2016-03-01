<?php
/**
 * @file
 *  Contains the GeoDetailsManager class.
 */

namespace AppBundle\Manager;

use Symfony\Component\HttpFoundation\Request;
use \Curl\Curl;

/**
 * Description of GeoDetailsManager
 *
 * @author edward
 */
class StationsFeedManager {
  
    const TRANSPORT_API_URL = 'http://transportapi.com/v3/uk/train/stations/bbox.json';

    /**
     *
     */
    public function __construct() {
        $this->app_id = '98b1d17e';
        $this->app_key = 'f4f457e81bb4f207fdfe0e418d5eec6f';
        $this->maxlat = '53.6';
        $this->maxlon = '-1.5';
        $this->minlat = '51.0';
        $this->minlon = '-5.5';
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

    $curl = new Curl();

    $curl->get(self::TRANSPORT_API_URL, $requestVars);

    if ($curl->error) {
        // @todo: Do some more intelligent logging here instead of an echo.
        // Also there are errors that we get that aren't curl errors.
        echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
        return NULL;
    }
    else {
        return $curl->response;
    }
  }
}

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
    $this->app_id = '03bf8009';
    $this->app_key = 'd9307fd91b0247c607e098d5effedc97';
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
  public function fetchStations() {
    $requestVars = array(
      'app_id' => $this->app_id,
      'app_key' => $this->app_key,
      'maxlat' => $this->maxlat,
      'maxlon' => $this->maxlon,
      'minlat' => $this->minlat,
      'minlon' => $this->minlon,
    );

    $curl = new Curl();

    $curl->get(self::TRANSPORT_API_URL, $requestVars);

    if ($curl->error) {
        echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
        return NULL;
    }
    else {
        return $curl->response;
    }
  }
}

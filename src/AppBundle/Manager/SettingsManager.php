<?php
/**
 * @file
 *  Contains the SettingsManager class.
 */

namespace AppBundle\Manager;

use Symfony\Component\Yaml\Parser;

/**
 * Handles settings contained in yaml files.
 *
 * @author edward
 */
class SettingsManager {
  const SETTINGS_FILE = 'settings';
  const SETTINGS_FILE_EXTENSION = 'yml';
  const SETTINGS_FILE_PATH = '/app/config/';
  
  /**
   * Constructor that fetches settings from yaml file.
   * 
   * @param type $parser
   */
  public function __construct($parser) {
    $this->parser = $parser;

    $fullFilepath = $_SERVER['DOCUMENT_ROOT'] . '/..' . self::SETTINGS_FILE_PATH . self::SETTINGS_FILE . '.' . self::SETTINGS_FILE_EXTENSION;
    $this->values = $this->parser->parse(file_get_contents($fullFilepath));
  }
  
  /**
   * Fetch the API app id.
   * 
   * @return string
   */
  public function getAppId() {
    return $this->values['api']['app_id'];
  }
  
  /**
   * Fetch the API app key.
   * 
   * @return string
   */
  public function getAppKey() {
    return $this->values['api']['app_key'];
  }
  
  /**
   * Fetch the period in seconds when the station data should be updated.
   * 
   * @return int
   */
  public function getFetchPeriod() {
    return $this->values['settings.config']['fetch_period'];
  }
}

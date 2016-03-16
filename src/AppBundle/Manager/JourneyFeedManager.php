<?php
/**
 * @file
 *  Contains the JourneyFeedManager class.
 */

namespace AppBundle\Manager;

/**
 * Class to manage the journey data feed.
 */
class JourneyFeedManager {

  const VAR_TRAIN_STATUS = 'passenger';
  const VAR_SAMPLE_TIME = '09:00';

  public function __construct($settingsManager) {
    // Service for handling app settings.
    $this->settingsManager = $settingsManager;
  }

  /**
   * Fetch a journey details from origin to destination.
   * 
   * @param type $origin_id
   * @param type $destination_id
   */
  public function fetchJourney($origin_code, $destination_code) {
    $requestVars = array(
      'app_id' => $this->settingsManager->getAppId(),
      'app_key' => $this->settingsManager->getAppKey(),
      'calling_at' => $destination_code,
      'train_status' => self::VAR_TRAIN_STATUS,
    );

    // We can add an error handling function like this, but not sure how to
    // use a method on a object.
    // $this->curl->errorFunction =
    $url = $this->settingsManager->getJourneyUrl() . '/' . $origin_code . '/' . $this->suitableDate() . '/' . $this->suitableTime() . 'timetable.json';
    $this->curl->get($url, $requestVars);

    return $this->curl;
  }

  /**
   * Returns a suitable sample date to look for a typical train journey.
   */
  protected function suitableDate() {
    // @todo: Work this out based on the next Monday that isn't a bank holiday.
    return '2016-05-02';
  }

  /**
   * Returns a suitable sample time to look for a typical train journey.
   */
  protected function suitableTime() {
    // Can probably stay constant.
    return self::VAR_SAMPLE_TIME;
  }
}

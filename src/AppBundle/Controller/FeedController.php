<?php

// src/AppBundle/Controller/FeedController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FeedController
 *
 * Controller for the /feed route.
 *
 * @package AppBundle\Controller
 */
class FeedController extends Controller
{
  /**
   * Action method for the /feed route.
   *
   * @return \AppBundle\Manager\Response
   */
  public function fetchAction() {
    return $this->get('app.stationsfeedmanager')->feedController();
  }
}
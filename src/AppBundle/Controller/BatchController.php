<?php
/**
 * @file
 *  Contains the BatchController class.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BatchController
 *
 * Controller for the /batch route.
 *
 * @package AppBundle\Controller
 */
class BatchController extends Controller
{ 
  /**
   * Action method for the /batch route.
   *
   * @return \AppBundle\Manager\Response
   */
  public function fetchAction() {
    $response = $this->get('app.batchmanager')->feedController();
    return $response;
  }
}
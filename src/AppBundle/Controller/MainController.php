<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Manager\StationsManager;

class MainController extends Controller
{
  /**
   * @Route("/map", name="map")
   */
  public function renderAction(Request $request)
  {
    //@todo: We should practice dependency injection here. If we make the 
    // Controller into a service, we can inject the dependency.
    $stationsManager = new StationsManager($this->get('doctrine.orm.entity_manager'));
    $stations = $stationsManager->getJsonStations();
    return $this->render('basemap.html.twig',
      array('stations' => $stations)
    );
  }
}

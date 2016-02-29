<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/map", name="map")
     */
    public function renderAction(Request $request)
    {
      $stations = 'Woo!';
      return $this->render('basemap.html.twig',
        array('stations' => $stations)
      );
    }
}

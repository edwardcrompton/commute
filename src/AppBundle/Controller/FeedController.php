<?php

// src/AppBundle/Controller/FeedController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends Controller
{
    public function fetchAction()
    {
        return new Response(
            '<html><body>Stations fetched.</body></html>'
        );
    }
}
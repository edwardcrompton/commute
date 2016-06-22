<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Manager;

/**
 * Class to provide essential dependency injection for FeedManagers.
 * 
 * Having read this:
 * @see http://richardmiller.co.uk/2011/04/18/symfony2-dependency-injection-types/
 * @see http://symfony.com/doc/current/components/dependency_injection/types.html
 * @see http://symfony.com/doc/2.8/components/dependency_injection/parentservices.html
 * I think the classes should perhaps be refactored to require fewer dependencies.
 */
class BaseFeedManager {
  
  // Services that will be injected into this class.
  public $entityManager;
  public $storageManager;
  public $requestManager;
  public $settingsManager;
  
  public function __construct($entityManager, $storageManager, $requestManager, $settingsManager) {
    $this->entityManager = $entityManager;
    $this->storageManager = $storageManager;
    $this->requestManager = $requestManager;
    $this->settingsManager = $settingsManager;
  }
}

<?php
/**
 * @file
 *  Contains the VariableController class.
 */

namespace AppBundle\Storage\Manager;

use AppBundle\Entity\Variable;

/**
 * Handles persistent variables.
 */
//@todo: Many of these classes feel as though they should be static.
// However, this has to extend controller to use getDoctrine and therefore
// has to be non static. See http://symfony.com/doc/2.8/book/doctrine.html for
// a reference to injecting Doctrine into a service.
class VariableManager
{
  protected $entityManager;

  // Dependency injection in action. In services.yml we specify that the
  // doctrine entity manager class should be passed in here.
  public function __construct($entityManager)
  {
    $this->entityManager = $entityManager; //$entityManager->getDoctrine()->getManager();
  }

  /**
   * 
   */
  public function setVar($name, $value) {
    if (!$variable = $this->loadVar($name)) {
      $variable = new Variable();
    }
    $variable->setName($name);
    $variable->setValue(serialize($value));
    $this->entityManager->persist($variable);
    $this->entityManager->flush();
  }
  
  /**
   * 
   * @param string $name
   * @return null
   */
  protected function loadVar($name) {
    $variable = $this->entityManager->getRepository('AppBundle:Variable')
      ->findOneByName($name);
    
    if (!$variable) {
      return NULL;
    }
    
    return $variable;
  }

  /**
   *
   */
  public function getVar($name, $value = NULL) {
    $variable = $this->loadVar($name);

    if ($variable != NULL) {
      return unserialize($variable->getValue());
    }

    return $value;
  }
}

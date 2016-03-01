<?php
/**
 * @file
 *  Contains the VariableController class.
 */

namespace AppBundle\Storage\Manager;

use AppBundle\Entity\Variable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Handles persistent variables.
 */
//@todo: Many of these classes feel as though they should be static.
// However, this has to extend controller to use getDoctrine and therefore
// has to be non static. See http://symfony.com/doc/2.8/book/doctrine.html for
// a reference to injecting Doctrine into a service.
class VariableManager
{  
  /**
   * 
   */
  public function setVar($name, $value) {
    $entityManager = $this->getDoctrine()->getManager();
    
    if (!$variable = $this->loadVar($name)) {
      $variable = new Variable();
    }
    $variable->setValue(serialize($value));
    $entityManager->persist($location);
    $entityManager->flush();
  }
  
  /**
   * 
   * @param string $name
   * @return null
   */
  protected function loadVar(string $name) {
    $variable = $this->getDoctrine()
      ->getRepository('AppBundle:Variable')
      ->findOneByName($name);
    
    if (!$variable) {
      return NULL;
    }
    
    return $variable->getValue();
  }

  /**
   *
   */
  public function getVar($name, $value = NULL) {
    if ($value = $this->loadVar($name)) {
      return $value;
    }

  }
}

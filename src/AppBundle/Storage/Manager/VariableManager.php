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
class VariableManager
{
  protected $entityManager;

  /**
   * Constructor to which the entity manager class gets injected.
   * 
   * @param type $entityManager
   */
  public function __construct($entityManager)
  {
    $this->entityManager = $entityManager;
  }

  /**
   * Sets the value for a new or existing persistent variable.
   * 
   * @param string $name
   *  The name of the persistent variable.
   * @param mixed $value
   *  A value that can be serialised and stored in the database.
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
   * Helper method to load a variable from the database.
   * 
   * @param string $name
   *  The name of the variable.
   * 
   * @return mixed or null if the variable cannot be fetched.
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
   * Gets the value of an existing persistent variable or a default value if it
   * has not yet been set.
   * 
   * @param string $name
   *  The name of the variable.
   * @param string $value
   *  The value to return if the variable does not exist. 
   * 
   * @return mixed
   *  The value of the variable returned from the database, or the default 
   *  value.
   */
  public function getVar($name, $value = NULL) {
    $variable = $this->loadVar($name);

    if ($variable != NULL) {
      return unserialize($variable->getValue());
    }

    return $value;
  }
}

<?php

/**
 * @file
 *  Contains the persistent entity class for route data.
 */

namespace AppBundle\Entity;

/**
 * Entity class to store persistent route data.
 */
class Route
{
  
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $origin_id;

    /**
     * @var integer
     */
    private $destination_id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set origin_id
     *
     * @param integer $originId
     * @return Route
     */
    public function setOriginId($originId)
    {
        $this->origin_id = $originId;

        return $this;
    }

    /**
     * Get origin_id
     *
     * @return integer 
     */
    public function getOriginId()
    {
        return $this->origin_id;
    }

    /**
     * Set destination_id
     *
     * @param integer $destinationId
     * @return Route
     */
    public function setDestinationId($destinationId)
    {
        $this->destination_id = $destinationId;
        return $this;
    }

    /**
     * Get destination_id
     *
     * @return integer 
     */
    public function getDestinationId()
    {
        return $this->destination_id;
    }
}

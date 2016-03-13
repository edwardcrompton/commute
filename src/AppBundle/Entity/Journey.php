<?php
/**
 * @file
 *  Contains the persistent entity class for journey data.
 */

namespace AppBundle\Entity;

/**
 * Entity class to store persistent journey data.
 */
class Journey
{
    protected $id;
    protected $origin_id;
    protected $destination_id;
    protected $duration;
    protected $price;

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
     * @return Journey
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
     * @return Journey
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

    /**
     * Set duration
     *
     * @param string $duration
     * @return Journey
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return string 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Journey
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }
}

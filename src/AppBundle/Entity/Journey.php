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
    private $route;
    private $sequence;
    protected $duration;

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
     * Set route
     *
     * @param integer $route
     * @return Journey
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return integer 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return Journey
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }
    /**
     * @var integer
     */
    private $station_id;


    /**
     * Set station_id
     *
     * @param integer $stationId
     * @return Journey
     */
    public function setStationId($stationId)
    {
        $this->station_id = $stationId;

        return $this;
    }

    /**
     * Get station_id
     *
     * @return integer 
     */
    public function getStationId()
    {
        return $this->station_id;
    }
}

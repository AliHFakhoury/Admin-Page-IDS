<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Event
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\eventRepository")
 * @ORM\Table(name="events")
 */

class Event{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $event_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $event_name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event_category_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event_holder_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $location;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="date")
     */
    protected $event_date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $max_attenders;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event_status_ID;

    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getEventID()
    {
        return $this->event_ID;
    }

    /**
     * @return mixed
     */
    public function getEventName()
    {
        return $this->event_name;
    }

    /**
     * @return mixed
     */
    public function getEventCategoryID()
    {
        return $this->event_category_ID;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getEventDate()
    {
        return $this->event_date;
    }

    /**
     * @return mixed
     */
    public function getMaxAttenders()
    {
        return $this->max_attenders;
    }

    /**
     * @return mixed
     */
    public function getEventStatusID()
    {
        return $this->event_status_ID;
    }

    /**
     * @param mixed $event_ID
     */
    public function setEventID($event_ID)
    {
        $this->event_ID = $event_ID;
    }

    /**
     * @param mixed $event_name
     */
    public function setEventName($event_name)
    {
        $this->event_name = $event_name;
    }

    /**
     * @param mixed $event_category_ID
     */
    public function setEventCategoryID($event_category_ID)
    {
        $this->event_category_ID = $event_category_ID;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param mixed $event_date
     */
    public function setEventDate($event_date)
    {
        $this->event_date = $event_date;
    }

    /**
     * @param mixed $max_attenders
     */
    public function setMaxAttenders($max_attenders)
    {
        $this->max_attenders = $max_attenders;
    }

    /**
     * @param mixed $event_status_ID
     */
    public function setEventStatusID($event_status_ID)
    {
        $this->event_status_ID = $event_status_ID;
    }

    /**
     * @return mixed
     */
    public function getEventHolderID()
    {
        return $this->event_holder_ID;
    }

    /**
     * @return mixed
     */
    public function getisDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted)
    {
        $this->is_deleted = $is_deleted;
    }


}

?>
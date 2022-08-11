<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EventAttender
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="event_attenders")
 */
class EventAttender{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_ID;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

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
    public function getUserID()
    {
        return $this->user_ID;
    }


}

?>
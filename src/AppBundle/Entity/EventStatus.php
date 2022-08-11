<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EventStatus
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="event_status")
 */

class EventStatus{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $event_status_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $event_status_name;

    /**
     * @return mixed
     */
    public function getEventStatusId()
    {
        return $this->event_status_id;
    }

    /**
     * @return mixed
     */
    public function getEventStatusName()
    {
        return $this->event_status_name;
    }


}

?>

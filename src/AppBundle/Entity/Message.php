<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Message
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="messages")
 */

class Message{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $message_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $isViewed;

    /**
     * @ORM\Column(type="integer")
     */
    protected $receiver_ID;

    /**
     * @return mixed
     */
    public function getMessageID()
    {
        return $this->message_ID;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->user_ID;
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getisViewed()
    {
        return $this->isViewed;
    }

    /**
     * @return mixed
     */
    public function getReceiverID()
    {
        return $this->receiver_ID;
    }


}

?>
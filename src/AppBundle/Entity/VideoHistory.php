<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class VideoHistory
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="video_history")
 */

class VideoHistory{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $video_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_ID;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

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
    public function getVideoID()
    {
        return $this->video_ID;
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
    public function getDate()
    {
        return $this->date;
    }


}

?>
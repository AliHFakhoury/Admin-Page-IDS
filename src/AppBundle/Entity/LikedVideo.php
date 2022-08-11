<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LikedVideo
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="liked_Videos")
 */

class likedVideo{
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


}
?>
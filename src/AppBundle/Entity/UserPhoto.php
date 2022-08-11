<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserPhoto
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user_photos")
 */

class UserPhoto{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $photo_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $photo_url;

    /**
     * @return mixed
     */
    public function getPhotoID()
    {
        return $this->photo_ID;
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
    public function getPhotoUrl()
    {
        return $this->photo_url;
    }


}

?>
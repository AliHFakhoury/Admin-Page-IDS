<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserFollower
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user_followers")
 */

class UserFollower{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $follower_id;

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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getFollowerId()
    {
        return $this->follower_id;
    }


}

?>
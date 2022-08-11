<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserStatus
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\userStatusRepository")
 * @ORM\Table(name="user_status")
 */

class UserStatus{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $user_status_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $user_status_name;

    /**
     * @return mixed
     */
    public function getUserStatusId()
    {
        return $this->user_status_id;
    }

    /**
     * @return mixed
     */
    public function getUserStatusName()
    {
        return $this->user_status_name;
    }


}

?>
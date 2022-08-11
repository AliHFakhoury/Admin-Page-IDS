<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Role
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */

class Role{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $role_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $role_name;

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * @return mixed
     */
    public function getRoleName()
    {
        return $this->role_name;
    }


}

?>
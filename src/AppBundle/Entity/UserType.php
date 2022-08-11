<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserType
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user_types")
 */

class UserType{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $typeID;

    /**
     * @ORM\Column(type="string")
     */
    protected $typeName;

    /**
     * @return mixed
     */
    public function getTypeID()
    {
        return $this->typeID;
    }

    /**
     * @return mixed
     */
    public function getTypeName()
    {
        return $this->typeName;
    }


}

?>
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class API
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="API")
 */

class API{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $api_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $api_name;

    /**
     * @return mixed
     */
    public function getApiID()
    {
        return $this->api_ID;
    }

    /**
     * @return mixed
     */
    public function getApiName()
    {
        return $this->api_name;
    }


}

?>
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Country
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="countries")
 */

class Country{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $country_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $country_name;

    /**
     * @return mixed
     */
    public function getCountryID()
    {
        return $this->country_ID;
    }

    /**
     * @return mixed
     */
    public function getCountryName()
    {
        return $this->country_name;
    }


}

?>
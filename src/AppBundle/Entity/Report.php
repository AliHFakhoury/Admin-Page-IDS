<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Report
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\reportsRepository")
 * @ORM\Table(name="reports")
 */

class Report{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

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
    public function getTitle()
    {
        return $this->title;
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
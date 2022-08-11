<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TechnicalIssue
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\techIssuesRepository")
 * @ORM\Table(name="technical_issues")
 */

class TechnicalIssue{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $table;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="string")
     */
    protected $ip;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;


    /**
     * @ORM\Column(type="string")
     */
    protected $browser;

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
    public function getUserID()
    {
        return $this->user_ID;
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getBrowser()
    {
        return $this->browser;
    }


}

?>

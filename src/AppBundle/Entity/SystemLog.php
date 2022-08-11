<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SystemLog
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\systemLogRepository")
 * @ORM\Table(name="system_log")
 */

class SystemLog{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    public $admin_id;
    
    /**
     * @ORM\Column(type="integer")
     */
    public $user_id;
    
    
    /**
     * @ORM\Column(type="string")
     */
    public $table_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $action;

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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
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
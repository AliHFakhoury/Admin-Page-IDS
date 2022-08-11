<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/10/2018
 * Time: 2:28 PM
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class systemPages
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\systemPageRepository")
 * @ORM\Table(name="system_pages")
 */
class systemPages{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $page_name;

    /**
     * @ORM\Column(type="integer")
     */
    protected  $role_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $page_url;

    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPageName()
    {
        return $this->page_name;
    }

    /**
     * @param mixed $page_name
     */
    public function setPageName($page_name)
    {
        $this->page_name = $page_name;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * @param mixed $role_id
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
    }

    /**
     * @return mixed
     */
    public function getPageUrl()
    {
        return $this->page_url;
    }

    /**
     * @param mixed $page_url
     */
    public function setPageUrl($page_url)
    {
        $this->page_url = $page_url;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted)
    {
        $this->is_deleted = $is_deleted;
    }



}
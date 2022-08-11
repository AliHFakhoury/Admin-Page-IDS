<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Permission
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\permissionRepository")
 * @ORM\Table(name="permissions")
 */

class Permission{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $permID;

    /**
     * @ORM\Column(type="string")
     */
    protected $userID;


    /**
     * @ORM\Column(type="integer")
     */
    protected $RoleID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $can_delete;

    /**
     * @ORM\Column(type="integer")
     */
    protected $can_edit;

    /**
     * @ORM\Column(type="integer")
     */
    protected $can_view;

    /**
     * @ORM\Column(type="integer")
     */
    protected $can_add;

    /**
     * @return mixed
     */
    public function getPermID()
    {
        return $this->permID;
    }

    /**
     * @return mixed
     */
    public function getRoleID()
    {
        return $this->RoleID;
    }

    public function setRoleId($roleId){
        $this->RoleID = $roleId;
    }

    public function setUserId($userId){
        $this->userID = $userId;
    }

    /**
     * @param mixed $permID
     */
    public function setPermID($permID)
    {
        $this->permID = $permID;
    }

    /**
     * @param mixed $can_delete
     */
    public function setCanDelete($can_delete)
    {
        $this->can_delete = $can_delete;
    }

    /**
     * @param mixed $can_edit
     */
    public function setCanEdit($can_edit)
    {
        $this->can_edit = $can_edit;
    }

    /**
     * @param mixed $can_view
     */
    public function setCanView($can_view)
    {
        $this->can_view = $can_view;
    }

    /**
     * @param mixed $can_add
     */
    public function setCanAdd($can_add)
    {
        $this->can_add = $can_add;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @return mixed
     */
    public function getCanDelete()
    {
        return $this->can_delete;
    }

    /**
     * @return mixed
     */
    public function getCanEdit()
    {
        return $this->can_edit;
    }

    /**
     * @return mixed
     */
    public function getCanView()
    {
        return $this->can_view;
    }

    /**
     * @return mixed
     */
    public function getCanAdd()
    {
        return $this->can_add;
    }



}

?>
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class admin
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\adminRepository")
 * @ORM\Table(name="admin_users")
 * @UniqueEntity(fields="username")
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class admin implements UserInterface {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $admin_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastName;


    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="integer")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="date")
     */
    protected $timeStamp;

    private $plainPassword;

    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getUsername(){
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getAdminID()
    {
        return $this->admin_ID;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return mixed
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * @param mixed $admin_ID
     */
    public function setAdminID($admin_ID)
    {
        $this->admin_ID = $admin_ID;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param mixed $timeStamp
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;
    }

    public function getRoles()
    {
        return [
            'ROLE_ADMIN',
        ];
    }
    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(){
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword){
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted)
    {
        $this->is_deleted = $is_deleted;
    }


}
?>
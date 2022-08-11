<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\userRepository")
 * @ORM\Table(name="users")
 */
class User{
    private $plainedPassword;
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $user_email;

    /**
     * @ORM\Column(type="string")
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $last_name;

    /**
     * @ORM\Column(type="string")
     */

    protected $password;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $api_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $country_id;

    /**
     * @ORM\Column(type="date")
     */
    protected $birth_date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $age;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status_id;

    /**
     * @ORM\Column(type="date")
     */
    protected $registration_Date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $category_id;

    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category_id;
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
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
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
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * @return mixed
     */
    public function getApiId()
    {
        return $this->api_id;
    }

    /**
     * @return mixed
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @return mixed
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * @return mixed
     */
    public function getRegistrationDate()
    {
        return $this->registration_Date;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @param mixed $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $type_id
     */
    public function setTypeId($type_id)
    {
        $this->type_id = $type_id;
    }

    /**
     * @param mixed $api_id
     */
    public function setApiId($api_id)
    {
        $this->api_id = $api_id;
    }

    /**
     * @param mixed $country_id
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;
    }

    /**
     * @param mixed $birth_date
     */
    public function setBirthDate($birth_date)
    {
        $this->birth_date = $birth_date;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @param mixed $status_id
     */
    public function setStatusId($status_id)
    {
        $this->status_id = $status_id;
    }

    /**
     * @param mixed $registration_Date
     */
    public function setRegistrationDate($registration_Date)
    {
        $this->registration_Date = $registration_Date;
    }


    /**
     * @return mixed
     */
    public function getPlainedPassword()
    {
        return $this->plainedPassword;
    }

    /**
     * @param mixed $plainedPassword
     */
    public function setPlainedPassword($plainedPassword)
    {
        $this->plainedPassword = $plainedPassword;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * @return mixed
     */
    public function getisDeleted()
    {
        return $this->is_deleted;
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
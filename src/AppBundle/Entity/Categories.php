<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Category
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\categoryRepository")
 * @ORM\Table(name="categories")
 */

class Categories{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $category_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $category_name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $parent_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $image_URL;

    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getCategoryID()
    {
        return $this->category_ID;
    }

    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        return $this->category_name;
    }

    /**
     * @return mixed
     */
    public function getParentID()
    {
        return $this->parent_ID;
    }

    /**
     * @return mixed
     */
    public function getImageURL()
    {
        return $this->image_URL;
    }

    /**
     * @param mixed $category_ID
     */
    public function setCategoryID($category_ID)
    {
        $this->category_ID = $category_ID;
    }

    /**
     * @param mixed $category_name
     */
    public function setCategoryName($category_name)
    {
        $this->category_name = $category_name;
    }

    /**
     * @param mixed $parent_ID
     */
    public function setParentID($parent_ID)
    {
        $this->parent_ID = $parent_ID;
    }

    /**
     * @param mixed $image_URL
     */
    public function setImageURL($image_URL)
    {
        $this->image_URL = $image_URL;
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
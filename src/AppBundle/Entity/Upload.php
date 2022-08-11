<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Upload
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="uploads")
 */

class Upload{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $upload_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $category_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $upload_title;

    /**
     * @ORM\Column(type="date")
     */
    protected $upload_date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status_ID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $nb_likes;

    /**
     * @ORM\Column(type="string")
     */
    protected $image_url;

    /**
     * @ORM\Column(type="string")
     */
    protected $video_url;

    /**
     * @ORM\Column(type="string")
     */
    protected $tags;

    /**
     * @return mixed
     */
    public function getUploadID()
    {
        return $this->upload_ID;
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
    public function getCategoryID()
    {
        return $this->category_ID;
    }

    /**
     * @return mixed
     */
    public function getUploadTitle()
    {
        return $this->upload_title;
    }

    /**
     * @return mixed
     */
    public function getUploadDate()
    {
        return $this->upload_date;
    }

    /**
     * @return mixed
     */
    public function getStatusID()
    {
        return $this->status_ID;
    }

    /**
     * @return mixed
     */
    public function getNbLikes()
    {
        return $this->nb_likes;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @return mixed
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }


}

?>
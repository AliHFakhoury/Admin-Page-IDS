<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UploadStatus
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="uploads_status")
 */

class UploadStatus{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $status_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $status_name;

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
    public function getStatusName()
    {
        return $this->status_name;
    }


}

?>
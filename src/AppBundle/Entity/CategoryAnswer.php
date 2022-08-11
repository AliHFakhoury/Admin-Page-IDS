<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CategoryAnswer
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="category_answers")
 */

class CategoryAnswer{

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
     * @ORM\Column(type="integer")
     */
    protected $question_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $answer;

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
    public function getQuestionID()
    {
        return $this->question_ID;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }


}

?>

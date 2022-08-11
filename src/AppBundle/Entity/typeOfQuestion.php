<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 7/24/2018
 * Time: 8:27 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Permission
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="typeOfQuestionRepository")
 * @ORM\Table(name="type_of_questions")
 */
class typeOfQuestion{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $type_question_id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $questionType;
    
    /**
     * @return mixed
     */
    public function getTypeQuestionId()
    {
        return $this->type_question_id;
    }
    
    /**
     * @param mixed $type_question_id
     */
    public function setTypeQuestionId($type_question_id)
    {
        $this->type_question_id = $type_question_id;
    }
    
    /**
     * @return mixed
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }
    
    /**
     * @param mixed $questionType
     */
    public function setQuestionType($questionType)
    {
        $this->questionType = $questionType;
    }

    
}
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CatQuestion
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\QuestionsRepository")
 * @ORM\Table(name="questions")
 */

class Questions{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $quest_ID;

    /**
     * @ORM\Column(type="string")
     */
    protected $quest_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $question;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $question_type_id;
    
    /**
     *@ORM\Column(type="integer")
     */
    protected $is_deleted;

    /**
     * @return mixed
     */
    public function getQuestID(){
        return $this->quest_ID;
    }

    /**
     * @return mixed
     */
    public function getQuestName(){
        return $this->quest_name;
    }

    /**
     * @return mixed
     */
    public function getQuestion(){
        return $this->question;
    }
    
    /**
     * @return mixed
     */
    public function getQuestionTypeId()
    {
        return $this->question_type_id;
    }
    
    
    /**
     * @param mixed $quest_ID
     */
    public function setQuestID($quest_ID){
        $this->quest_ID = $quest_ID;
    }

    /**
     * @param mixed $quest_name
     */
    public function setQuestName($quest_name){
        $this->quest_name = $quest_name;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question){
        $this->question = $question;
    }

    /**
     * @param mixed $question_type_id
     */
    public function setQuestionType($question_type_id){
        $this->question_type_id = $question_type_id;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted){
        $this->is_deleted = $is_deleted;
    }
}

?>

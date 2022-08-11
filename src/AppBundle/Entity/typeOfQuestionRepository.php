<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 7/31/2018
 * Time: 7:21 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class typeOfQuestionRepository extends EntityRepository {
    public function findTypeByQuestionId($questionId){
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = "SELECT type_question_id FROM type_of_questions
                INNER JOIN questions
                    ON type_of_questions.type_question_id = questions.question_type_id
                    WHERE questions.quest_id = ".$questionId;
    
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(7);
    }
}
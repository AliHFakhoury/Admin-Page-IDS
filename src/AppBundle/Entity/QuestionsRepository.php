<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/8/2018
 * Time: 11:12 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class QuestionsRepository extends EntityRepository {
    public function findAllQuestions($data, $perPage, $pageNumber){
        $questions = array();

        $conn = $this->getEntityManager()->getConnection();

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('q')
            ->from('AppBundle:Questions', 'q');

        if(isset($data["QuestionName"])){
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["QuestionName"])) {
                $qb = $qb->andWhere('q.quest_name LIKE \'' . $data["QuestionName"] . '%\'');
            }
        }

        if(isset($data["Question"])){
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["Question"])) {
                $qb = $qb->andWhere('q.question LIKE \'' . $data["Question"] . '%\'');
            }
        }

        if(isset($data["Category"])){
            dump($data["Category"]);
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["Category"])) {
                $qb = $qb->innerJoin(CategoryQuestions::class,'c','WITH','c.quest_id = q.quest_ID');
                $qb = $qb->innerJoin(Categories::class, 'cat', 'WITH','c.category_id = cat.category_ID');
                $qb = $qb->andWhere('c.category_id = '.$data["Category"]);
            }
        }

        $qb = $qb->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->andWhere('q.is_deleted != 1');

        $qb = $qb->getQuery()->execute();

        for($i = 0; $i < count($qb); $i++){
            $sql = "SELECT category_name FROM categories
                    INNER JOIN questions_in_category
                          ON categories.category_id = questions_in_category.category_id 
                          WHERE questions_in_category.quest_id = ".$qb[$i]->getQuestID();

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $categories = $stmt->fetchAll(7);

            $sql2 = "SELECT question_type FROM type_of_questions
                     INNER JOIN questions
                           ON type_of_questions.type_question_id = questions.question_type_id
                      WHERE questions.quest_id = ".$qb[$i]->getQuestID();
    
            $stmt = $conn->prepare($sql2);
            $stmt->execute();
            $questionType = $stmt->fetch(7);
    
            $questionObject = [
                'quest_id' => $qb[$i]->getQuestID(),
                'quest_name' => $qb[$i]->getQuestName(),
                'categories' => implode(", ",$categories),
                'question' => $qb[$i]->getQuestion(),
                'type' => $questionType,
            ];

            array_push($questions, $questionObject);
        }

        return $questions;
    }

    public function countPages($data, $perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(q)')
            ->from('AppBundle:Questions', 'q');

        if(isset($data["QuestionName"])){
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["QuestionName"])) {
                $qb = $qb->andWhere('q.quest_name LIKE \'' . $data["QuestionName"] . '%\'');
            }
        }

        if(isset($data["Question"])){
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["Question"])) {
                $qb = $qb->andWhere('q.question LIKE \'' . $data["Question"] . '%\'');
            }
        }

        if(isset($data["Category"])){
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["Category"])) {
                $qb = $qb->innerJoin(CategoryQuestions::class,'c','WITH','c.quest_id = q.quest_ID');
                $qb = $qb->innerJoin(Categories::class, 'cat', 'WITH','c.category_id = cat.category_ID');
                $qb = $qb->andWhere('c.category_id = '.$data["Category"]);
            }
        }

        $qb = $qb->andWhere('q.is_deleted != 1');
        $qb = $qb->getQuery()->execute();

        $numberPages = ceil($qb[0][1] / $perPage);

        return $numberPages;
    }
    
    public function changeTypeOfQuestion($questionId, $typeId){
        $qb = $this->getEntityManager()->createQueryBuilder()
                ->update('AppBundle:Questions','q')
                ->set('q.question_type_id', $typeId)
                ->where('q.quest_ID = '.$questionId);
        
        $qb = $qb->getQuery();
        
        if($qb->execute()){
            return true;
        }
        
        return false;
    }
}
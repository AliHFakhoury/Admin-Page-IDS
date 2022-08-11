<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CatQuestion
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CategoryQuestionRepository")
 * @ORM\Table(name="questions_in_category")
 */

class CategoryQuestions{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */

    protected $quest_id;
    /**
     * @ORM\Column(type="integer")
     */
    protected $category_id;

    /**
     * @return mixed
     */
    public function getQuestID()
    {
        return $this->quest_id;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param mixed $quest_id
     */
    public function setQuestID($quest_id)
    {
        $this->quest_id = $quest_id;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }
}

?>

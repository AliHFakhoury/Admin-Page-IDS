<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/14/2018
 * Time: 11:32 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class AdminCategoriesRepository extends EntityRepository{
    public function getUserCategories($userID){
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT category_id FROM admin_categories WHERE user_id = ".$userID;

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(7);
    }
}
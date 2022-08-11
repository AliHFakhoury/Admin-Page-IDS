<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 23/11/2017
 * Time: 9:03 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class categoryRepository extends EntityRepository{
    public function findAllCriteria($data, $pageNumber, $perPage){
        $categories = array();
        $conn = $this->getEntityManager()->getConnection();

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Categories', 'c');

        if (isset($data["categoryName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["categoryName"])) {
                $qb = $qb->andWhere('c.category_name LIKE \'%' . $data["categoryName"] . '%\''); //print an error or something if one of them exists.
            }
        }

        $qb = $qb->setFirstResult($perPage * ($pageNumber - 1))
            ->setMaxResults($perPage);

        $qb = $qb->andWhere('c.is_deleted != 1');

        $qb = $qb->getQuery()->execute();
        for ($i = 0; $i < sizeof($qb); $i++) {
            $sql = 'SELECT category_name, category_id, parent_id
              FROM categories
              WHERE category_id = ' . $qb[$i]->getCategoryID();

            $sql2 = 'SELECT category_name
              FROM categories
              WHERE category_id = ' . $qb[$i]->getParentID();

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $catName = $stmt->fetch();

            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute();
            $parentName = $stmt2->fetch();

            $category = array();

            $category["category_id"] = $catName["category_id"];
            if (!empty($catName)) {
                $category["category_name"] = $catName["category_name"];
            }

            if (!empty($parentName)) {
                $category["parent_name"] = $parentName["category_name"];
            }else if($catName["parent_id"] == -1){
                $category["parent_name"] = "Is Parent";
            }else{
                $category["parent_name"] = "";
            }

            array_push($categories, $category);
        }

        return $categories;
    }

    public function countPages($perPage, $categories){
        $conn = $this->getEntityManager()->getConnection();
        if(sizeof($categories["category"]) == 1){
            $category = Array($categories);
            $query = "WHERE (category_id = " . implode(" OR category_id = ", $category);
        }else{
            $query = "WHERE (category_id = " . implode(" OR category_id = ", $categories["category"]);
        }

        $sql = 'SELECT COUNT(*) FROM categories ';
        $sql = $sql.$query.')';

        if(isset($categories['categoryName'])){
            $sql = $sql . " AND category_name LIKE '".$categories['categoryName']."'";
        }

        $sql = $sql." AND is_deleted != 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $records = $stmt->fetch();
        $pages = intval(ceil($records["COUNT(*)"] / $perPage));

        return $pages;
    }

    public function findByID($data){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Categories', 'c')
            ->where('c.category_ID = '.$data);

        $qb = $qb->getQuery()->execute();

        return $qb[0];
    }

    public function findUsersCategory($data){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Categories', 'c');

        if (sizeof($data) == 1) {
            $query = "c.parent_ID = " .$data[0];
        } else {
            $query = "c.parent_ID = " . implode(" OR c.parent_ID = ", $data);
            $qb = $qb->andWhere($query);
        }

        $qb = $qb->andWhere($query);

        $qb = $qb->getQuery()->execute();
        return $qb;
    }

    /*
     * gets them as category objects
     */
    public function getParentCategories(){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Categories', 'c')
            ->where('c.parent_ID = -1');

        $qb = $qb->getQuery()->execute();

        return $qb;
    }

    public function getCategoriesForAdmin(){
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT category_id FROM categories WHERE parent_id != -1";

        $sql = $conn->prepare($sql);
        $sql->execute();

        $sql = $sql->fetchAll(7);
        return $sql;
    }

    /*
     * gets categories in an array
     */
    public function getParentCategoriesForAdmin(){
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT category_id FROM categories WHERE parent_id = -1";

        $sql = $conn->prepare($sql);
        $sql->execute();

        $sql = $sql->fetchAll(7);
        return $sql;
    }

    public function getAllCategoriesAdmin(){
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM categories WHERE parent_id = -1";

        $sql = $conn->prepare($sql);
        $sql->execute();

        $sql = $sql->fetchAll();

        return $sql;
    }

    public function findNotParentCategories(){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Categories','c')
            ->andWhere('c.parent_ID != -1');

        $qb = $qb->getQuery()->execute();

        return $qb;
    }
}
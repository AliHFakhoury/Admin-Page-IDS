<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\user;

class userRepository extends EntityRepository{
    public function findAllCriteria($data, $pageNumber, $perPage){
        $users = array();

        $conn = $this->getEntityManager()->getConnection();

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('AppBundle:User', 'u');

        if (isset($data["Name"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["Name"])) {
                $qb = $qb->andWhere('u.first_name LIKE \'' . $data["Name"] . '%\'');
                $qb = $qb->orWhere('u.last_name LIKE \'' . $data["Name"] . '%\'');
            }
        }

        if (isset($data["lastName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["lastName"])) {
            }
        }

        if (isset($data["email"])) {
            if (!preg_match('/[\'^£$%&*()}{#~?><>,|=_+¬-]/', $data["email"])) {
                $qb = $qb->andWhere('u.user_email LIKE \'' . $data["email"] . '%\''); //print an error or something if one of them exists.
            }
        }

        if (isset($data["status"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["status"])) {
                $qb = $qb->andWhere('u.status_id = ' . $data["status"]);
            }
        }

        if (isset($data["category"])) {
            if(sizeof($data["category"]) == 1){
                $category = Array($data["category"]);
                $query = "u.category_id = ".$category[0];
            }else{
                $query = "u.category_id = " . implode(" OR u.category_id = ", $data["category"]);
            }

            $qb = $qb->andWhere($query);
        }else if(isset($data["parent"])){
            if(count($data["parent"]) == 1){
                dump($data["parent"]);
                $qb = $qb->innerJoin(Categories::class, 'c', 'WITH', 'c.parent_ID = '.$data["parent"][0]);
            }else{
                $query = "c.parent_ID = " . implode(" OR c.parent_ID = ", $data["parent"]);
                $qb = $qb->innerJoin(Categories::class, 'c', 'WITH', $query);
            }
        }

        if (isset($data["from"], $data["to"])) {
            $qb = $qb->andWhere('u.registration_Date between \'' . $data["from"]->format('Y-m-d H:i:s') . '\' and \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        } else if (isset($data["from"]) && !isset($data["to"])) {
            $qb = $qb->andWhere('u.registration_Date > \'' . $data["from"]->format('Y-m-d H:i:s').'\'');
        } else if (!isset($data["from"]) && isset($data["to"])) {
            $qb = $qb->andWhere('u.registration_Date < \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        }

        $qb = $qb->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->andWhere('u.is_deleted != 1');

        if(isset($data["parent"]) && !isset($data["category"])){
            $qb = $qb->andWhere('u.category_id = c.category_ID');
        }

        $qb = $qb->getQuery()->execute();

        for($i = 0; $i < sizeof($qb); $i++){
            $sql ='SELECT users.user_id, users.first_name, users.last_name, users.user_email, users.registration_date, user_status.user_status_name, countries.country_name,
                  categories.category_name
            FROM users
            JOIN user_status
              ON user_status.user_status_id = '.$qb[$i]->getStatusId().'
            JOIN countries
              ON countries.country_id = '.$qb[$i]->getCountryId().'
            JOIN categories
              ON categories.category_id = '.$qb[$i]->getCategory().'
            WHERE users.user_ID = '.$qb[$i]->getUserId();

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $user = $stmt->fetch();

            if(!empty($user)){
                array_push($users, $user);
            }
        }

        return $users;
    }


    public function findByPage($page, $perPage){
        $users = array();
        $conn = $this->getEntityManager()->getConnection();
        $repo = $this->getEntityManager()->getRepository('AppBundle:User');

        $AllUsers = $repo->findBy(
            array(),
            array(),
            $perPage,
            $perPage*($page-1)
        );

        for($i = 0; $i < sizeof($AllUsers); $i++){
            $sql ='SELECT users.user_id, users.first_name, users.last_name, users.registration_date, user_status.user_status_name, countries.country_name,
                  categories.category_name
            FROM users
            JOIN user_status
              ON user_status.user_status_id = '.$AllUsers[$i]->getStatusId().'
            JOIN countries
              ON countries.country_id = '.$AllUsers[$i]->getCountryId().'
            JOIN categories
              ON categories.category_id = '.$AllUsers[$i]->getCategory().'
            WHERE users.user_ID = '.$AllUsers[$i]->getUserId();

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $user = $stmt->fetch();

            if(!empty($user)){
                array_push($users, $user);
            }
        }

        return $users;

    }

    public function countPages($data, $perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u)')
            ->from('AppBundle:User', 'u');

        if (isset($data["Name"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["Name"])) {
                $qb = $qb->andWhere('u.first_name LIKE \'' . $data["Name"] . '%\'');
                $qb = $qb->orWhere('u.last_name LIKE \'' . $data["Name"] . '%\'');
            }
        }

        if (isset($data["email"])) {
            if (!preg_match('/[\'^£$%&*()}{ #~?><>,|=_+¬-]/', $data["email"])) {
                $qb = $qb->andWhere('u.user_email LIKE \'' . $data["email"] . '%\''); //print an error or something if one of them exists.
            }
        }

        if (isset($data["status"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["status"])) {
                $qb = $qb->andWhere('u.status_id = ' . $data["status"]);
            }
        }

        if (isset($data["category"])) {
            if(sizeof($data["category"]) == 1){
                $category = Array($data["category"]);
                $query = "u.category_id = " . implode(" OR u.category_id = ", $category);
            }else{
                $query = "u.category_id = " . implode(" OR u.category_id = ", $data["category"]);
            }
            $qb = $qb->andWhere($query);
        }else if(isset($data["parent"])){
            if(count($data["parent"]) == 1){
                $qb = $qb->innerJoin(Categories::class, 'c', 'WITH', 'c.parent_ID = '.$data["parent"][0]);
            }else{
                $query = "c.parent_ID = " . implode(" OR c.parent_ID = ", $data["parent"]);
                $qb = $qb->innerJoin(Categories::class, 'c', 'WITH', $query);
            }
        }

        if (isset($data["from"], $data["to"])) {
            $qb = $qb->andWhere('u.registration_Date between \'' . $data["from"]->format('Y-m-d H:i:s') . '\' and \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        } else if (isset($data["from"]) && !isset($data["to"])) {
            $qb = $qb->andWhere('u.registration_Date > \'' . $data["from"]->format('Y-m-d H:i:s').'\'');
        } else if (!isset($data["from"]) && isset($data["to"])) {
            $qb = $qb->andWhere('u.registration_Date < \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        }

        $qb = $qb->andWhere('u.is_deleted != 1');

        if(isset($data["parent"]) && !isset($data["category"])){
            $qb = $qb->andWhere('u.category_id = c.category_ID');
        }
        $qb = $qb->getQuery()->execute();

        $numberPages = ceil($qb[0][1] / $perPage);
        return $numberPages;
    }

    public function findByIDView($id){
        $user = $this->find($id);
        $conn = $this->getEntityManager()->getConnection();

        $sql ='SELECT users.user_id, users.age, users.user_email, users.birth_date, users.first_name, users.last_name, users.registration_date, user_status.user_status_name, countries.country_name,
                  categories.category_name
            FROM users
            JOIN user_status
              ON user_status.user_status_id = '.$user->getStatusId().'
            JOIN countries
              ON countries.country_id = '.$user->getCountryId().'
            JOIN categories
              ON categories.category_id = '.$user->getCategory().'
            WHERE users.user_ID = '.$user->getUserId();

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $userData = $stmt->fetch();

        return $userData;
    }
}
?>
<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Lexer;

class eventRepository extends EntityRepository{
    public function findAllCriteria($data, $pageNumber, $perPage){
        $events = array();

        $conn = $this->getEntityManager()->getConnection();

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from('AppBundle:Event', 'e');

        if (isset($data["eventName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["eventName"])) {
                $qb = $qb->andWhere('e.event_name LIKE \'' . $data["eventName"] . '%\''); //print an error or something if one of them exists.
            }
        }

        if (isset($data["status"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["status"])) {
                $qb = $qb->andWhere('e.event_status_ID = ' . $data["status"]);
            }
        }

        if (isset($data["category"])) {
            if(sizeof($data["category"]) == 1){
                $category = Array($data["category"]);
                $query = "e.event_category_ID = " . implode(" OR e.event_category_ID = ", $category);
            }else{
                $query = "e.event_category_ID = " . implode(" OR e.event_category_ID = ", $data["category"]);
            }
            $qb = $qb->andWhere($query);
        }else if(isset($data["parent"])){
            if(count($data["parent"]) == 1){
                $qb = $qb->innerJoin(Categories::class, 'c', 'WITH', 'c.parent_ID = '.$data["parent"]);
            }else{
                $query = "c.parent_ID = " . implode(" OR c.parent_ID = ", $data["parent"]);
                $qb = $qb->innerJoin(Categories::class, 'c', 'WITH', $query);
            }

        }

        if(isset($data["holder_name"])){
            $qb = $qb->innerJoin(User::class,'u','WITH','u.user_id = e.event_holder_ID');
            $qb = $qb->andwhere('u.first_name LIKE \'%'.$data["holder_name"].'%\'');
            $qb = $qb->orwhere('u.last_name LIKE \'%'.$data["holder_name"].'%\'');
        }

        if (isset($data["from"], $data["to"])) {
            $qb = $qb->andWhere('e.event_date between \'' . $data["from"]->format('Y-m-d H:i:s') . '\' and \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        } else if (isset($data["from"]) && !isset($data["to"])) {
            $qb = $qb->andWhere('e.event_date > \'' . $data["from"]->format('Y-m-d H:i:s').'\'');
        } else if (!isset($data["from"]) && isset($data["to"])) {
            $qb = $qb->andWhere('e.event_date < \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        }

        $qb = $qb->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->andWhere('e.is_deleted != 1');

        if(isset($data["parent"]) && !isset($data["category"])){
            $qb = $qb->andWhere('e.event_category_ID = c.category_ID');
        }

        $qb = $qb->getQuery()->execute();

        for($i = 0; $i < sizeof($qb); $i++){
            $sql ='SELECT events.event_id, events.event_name, events.location, events.max_attenders, event_status.event_status_name,
            events.event_date, users.first_name, users.last_name , categories.category_name
            FROM events
            JOIN event_status
              ON event_status.event_status_id = '.$qb[$i]->getEventStatusID().'
            JOIN categories
              ON categories.category_id = '.$qb[$i]->getEventCategoryID().'
              JOIN users
              ON users.user_id = '.$qb[$i]->getEventHolderID().'
            WHERE events.event_id = '.$qb[$i]->getEventID();

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $eventInfo = $stmt->fetch();
            if(!empty($eventInfo)){
                array_push($events, $eventInfo);
            }
        }

        $newArray = array();
        array_push($newArray, $events);
        return $newArray;
    }


    public function countPages($data, $perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(e)')
            ->from('AppBundle:Event', 'e');

        if(isset($data["holder_name"])){
            $qb = $qb->innerJoin(User::class,'u','WITH','u.user_id = e.event_holder_ID');
            $qb = $qb->andwhere('u.first_name LIKE \'%'.$data["holder_name"].'%\'');
            $qb = $qb->orwhere('u.last_name LIKE \'%'.$data["holder_name"].'%\'');
        }

        if (isset($data["eventName"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["eventName"])) {
                $qb = $qb->andWhere('e.event_name LIKE \'% ' . $data["eventName"] . '%\'');
            }
        }

        if (isset($data["status"])) {
            if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $data["status"])) {
                $qb = $qb->andWhere('e.event_status_ID = ' . $data["status"]);
            }
        }

        if (isset($data["category"])) {
                if(sizeof($data["category"]) == 1){
                    $category = Array($data["category"]);
                    $query = "e.event_category_ID = " . implode(" OR e.event_category_ID = ", $category);
                }else{
                    $query = "e.event_category_ID = " . implode(" OR e.event_category_ID = ", $data["category"]);
                }
                $qb = $qb->andWhere($query);
        }

        if (isset($data["from"], $data["to"])) {
            $qb = $qb->andWhere('e.event_date between \'' . $data["from"]->format('Y-m-d H:i:s') . '\' and \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        } else if (isset($data["from"]) && !isset($data["to"])) {
            $qb = $qb->andWhere('e.event_date > \'' . $data["from"]->format('Y-m-d H:i:s').'\'');
        } else if (!isset($data["from"]) && isset($data["to"])) {
            $qb = $qb->andWhere('e.event_date < \'' . $data["to"]->format('Y-m-d H:i:s') . '\'');
        }

        $qb = $qb->andWhere('e.is_deleted != 1');
        $qb = $qb->getQuery()->execute();


        $numberPages = ceil($qb[0][1] / $perPage);

        return $numberPages;
    }
}
















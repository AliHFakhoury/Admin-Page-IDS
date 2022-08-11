<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/17/2018
 * Time: 6:18 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class systemLogRepository extends EntityRepository{
    public function findAllByPage($perPage, $pageNumber){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from('AppBundle:SystemLog', 's')
            ->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->getQuery()->execute();
        return $qb;
    }

    public function countPages($perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(s)')
            ->from('AppBundle:SystemLog', 's');

        $qb = $qb->getQuery()->execute();
        $count = ceil($qb[0][1]/$perPage);

        return $count;
    }
    
    public function addSystemLog($adminID, $userID, $tableName, $action, $ip){
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = "INSERT INTO system_log(user_id,table_name,action,admin_id, timestamp, ip) VALUES("."".$userID.",'".$tableName."','".$action."',".$adminID.",'".date('Y-m-d H:i:s',$_SERVER["REQUEST_TIME"]).
            "','".$ip."')";
        
        $conn->prepare($sql);
        
        $conn->executeQuery($sql);
    }
}
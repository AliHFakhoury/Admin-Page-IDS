<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/17/2018
 * Time: 6:07 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class techIssuesRepository extends EntityRepository{
    public function findAllByPage($perPage, $pageNumber){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from('AppBundle:TechnicalIssue', 't')
            ->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->getQuery()->execute();

        return $qb;
    }

    public function countPages($perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(t)')
            ->from('AppBundle:TechnicalIssue', 't');

        $qb = $qb->getQuery()->execute();
        $count = ceil($qb[0][1]/$perPage);

        return $count;
    }
    
    public function addTechnicalIssue($adminID, $userID, $tableName, $description, $ip){
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = "INSERT INTO technical_issues(user_id,table,description,admin_id, timestamp, ip) VALUES("."".$userID.",'".$tableName."','".$description."',".$adminID.",'".date('Y-m-d H:i:s',$_SERVER["REQUEST_TIME"]).
            "','".$ip."')";
        
        $conn->prepare($sql);
        
        $conn->executeQuery($sql);
    }
}
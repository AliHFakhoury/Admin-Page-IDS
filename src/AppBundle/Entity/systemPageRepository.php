<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/11/2018
 * Time: 4:41 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class systemPageRepository extends EntityRepository {
    public function findRolesByPageId($pageID){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('s.role_id')
            ->from('AppBundle:systemPages', 's');

        $qb = $qb->where('s.id = '.$pageID);

        $qb = $qb->getQuery()->execute();
        return $qb[0];
    }

    public function getSystemPagesByUserID($userID){
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT page_name, page_url FROM  system_pages INNER JOIN users_roles ON users_roles.role_id = system_pages.role_id WHERE users_roles.user_id = ".$userID;

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getSystemPagesForTable($userID){
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT page_name, page_url, system_pages.id, roles.role_name  FROM  system_pages 
                  INNER JOIN users_roles 
                  ON users_roles.role_id = system_pages.role_id 
                  INNER JOIN roles
                  ON roles.role_id = users_roles.role_id
                  WHERE system_pages.is_deleted != 1 AND users_roles.user_id = ".$userID;

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
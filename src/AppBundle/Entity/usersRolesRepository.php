<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/16/2018
 * Time: 6:30 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class usersRolesRepository extends EntityRepository{
    public function getRolesById($userId){
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT role_id FROM users_roles WHERE user_id = '".$userId."'";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $roles = $stmt->fetchAll(7);

        return $roles;
    }

    public function getRoleByUserIDAndRoleID($userId, $roleId){
        $qb = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('AppBundle:UsersRoles', 'u')
                ->where('u.user_id = '.$userId)
                ->andWhere('u.role_id = '.$roleId);

        $qb = $qb->getQuery();
        $qb->execute();
        return $qb->getResult()[0];
    }
}
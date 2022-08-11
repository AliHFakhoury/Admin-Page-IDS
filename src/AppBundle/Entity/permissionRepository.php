<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/11/2018
 * Time: 5:30 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class permissionRepository extends EntityRepository {
    public function findByUserId($userId){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Permission', 'p');

        $qb = $qb->where('p.userID = '.$userId);

        $qb = $qb->getQuery()->execute();

        return $qb;
    }

    public function findByUserRoleId($userId, $roleId){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Permission', 'p');

        $qb = $qb->where('p.userID = '.$userId)
                 ->andWhere('p.RoleID = '. $roleId['role_id']);

        $qb = $qb->getQuery()->execute();

        return sizeof($qb) > 0;
    }
}
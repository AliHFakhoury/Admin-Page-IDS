<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/9/2018
 * Time: 12:02 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class userStatusRepository extends EntityRepository{
    public function findStatus(){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('us')
            ->from('AppBundle:UserStatus','us')
            ->where('us.user_status_id != 2');

        $qb = $qb->getQuery()->execute();

        return $qb;
    }
}
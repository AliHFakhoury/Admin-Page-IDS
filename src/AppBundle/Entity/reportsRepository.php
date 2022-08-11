<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 1/17/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class reportsRepository extends EntityRepository{
    public function findAllByPage($perPage, $pageNumber){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from('AppBundle:Report', 'r')
            ->setFirstResult($perPage*($pageNumber-1))
            ->setMaxResults($perPage);

        $qb = $qb->getQuery()->execute();

        return $qb;
    }

    public function countPages($perPage){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(r)')
            ->from('AppBundle:Report', 'r');

        $qb = $qb->getQuery()->execute();
        $count = ceil($qb[0][1]/$perPage);

        return $count;
    }
}
<?php

namespace AppBundle\Repository;

/**
 * CreditRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CreditRepository extends \Doctrine\ORM\EntityRepository
{
    public function count()
    {
        $db = $this
            ->createQueryBuilder('c')
            ->select('count(c.id)');

        return $db->getQuery()->getSingleScalarResult();
    }

    public function getDataChart1()
    {
        $db = $this
            ->createQueryBuilder('c')
            ->select('c.date, COUNT(c.date) as total, COUNT(DISTINCT c.client) as client')
            ->groupBy('c.date');

        return $db->getQuery()->getResult();
    }

    public function getDataChart2()
    {
        $db = $this
            ->createQueryBuilder('c')
            ->select('c.date, CASE  cp.measurementUnit WHEN 3 THEN SUM(cp.amount * (cp.quantity / 1000))  ELSE SUM(cp.amount * cp.quantity) END as total')
            ->innerJoin('c.creditProduct', 'cp')
            ->groupBy('c.date');

        return $db->getQuery()->getResult();
    }

    public function getDataChart3($id)
    {
        $db = $this
            ->createQueryBuilder('c')
            ->select('c.date, SUM(cp.amount * cp.quantity) as total')
            ->innerJoin('c.creditProduct', 'cp')
            ->innerJoin('c.client', 'cl')
            ->where('cl.id = :id')
            ->groupBy('c.date')
            ->setParameter('id', $id);

        return $db->getQuery()->getResult();
    }
}

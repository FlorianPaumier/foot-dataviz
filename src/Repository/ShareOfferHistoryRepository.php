<?php

namespace App\Repository;

use App\Entity\ShareOfferHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ShareOfferHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShareOfferHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShareOfferHistory[]    findAll()
 * @method ShareOfferHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShareOfferHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ShareOfferHistory::class);
    }

    // /**
    //  * @return ShareOfferHistory[] Returns an array of ShareOfferHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShareOfferHistory
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

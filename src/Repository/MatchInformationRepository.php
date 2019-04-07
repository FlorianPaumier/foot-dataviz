<?php

namespace App\Repository;

use App\Entity\MatchInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MatchInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatchInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatchInformation[]    findAll()
 * @method MatchInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchInformationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MatchInformation::class);
    }

    // /**
    //  * @return MatchInformation[] Returns an array of MatchInformation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MatchInformation
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

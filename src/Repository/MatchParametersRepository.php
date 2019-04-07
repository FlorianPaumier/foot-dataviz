<?php

namespace App\Repository;

use App\Entity\MatchParameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MatchParameters|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatchParameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatchParameters[]    findAll()
 * @method MatchParameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchParametersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MatchParameters::class);
    }

    // /**
    //  * @return MatchParameters[] Returns an array of MatchParameters objects
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
    public function findOneBySomeField($value): ?MatchParameters
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

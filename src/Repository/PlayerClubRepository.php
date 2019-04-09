<?php

namespace App\Repository;

use App\Entity\PlayerClub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlayerClub|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerClub|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerClub[]    findAll()
 * @method PlayerClub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerClubRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlayerClub::class);
    }

    // /**
    //  * @return PlayerClub[] Returns an array of PlayerClub objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlayerClub
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

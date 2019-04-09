<?php

namespace App\Repository;

use App\Entity\PlayerAttribut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlayerAttribut|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerAttribut|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerAttribut[]    findAll()
 * @method PlayerAttribut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerAttributRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlayerAttribut::class);
    }

    // /**
    //  * @return PlayerAttribut[] Returns an array of PlayerAttribut objects
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
    public function findOneBySomeField($value): ?PlayerAttribut
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

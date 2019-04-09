<?php

namespace App\Repository;

use App\Entity\PlayerInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlayerInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerInformation[]    findAll()
 * @method PlayerInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerInformationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlayerInformation::class);
    }

    // /**
    //  * @return PlayerInformation[] Returns an array of PlayerInformation objects
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
    public function findOneBySomeField($value): ?PlayerInformation
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

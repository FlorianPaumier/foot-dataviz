<?php


namespace App\Repository;

use App\Entity\Actor;
use App\Entity\Company;
use App\Entity\Writer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Writer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Writer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Writer[]    findAll()
 * @method Writer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WriterRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Writer::class);
    }

    public function findByName(string $name)
    {
        try {
            return $this->createQueryBuilder("w")
                ->andWhere("w.people.name = :name")->setParameter("name", $name)
                ->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
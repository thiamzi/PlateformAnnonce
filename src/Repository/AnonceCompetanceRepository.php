<?php

namespace App\Repository;

use App\Entity\AnonceCompetance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AnonceCompetance|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnonceCompetance|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnonceCompetance[]    findAll()
 * @method AnonceCompetance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnonceCompetanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnonceCompetance::class);
    }

    // /**
    //  * @return AnonceCompetance[] Returns an array of AnonceCompetance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnonceCompetance
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

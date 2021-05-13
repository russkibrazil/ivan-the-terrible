<?php

namespace App\Repository;

use App\Entity\CriancaVinculo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CriancaVinculo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CriancaVinculo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CriancaVinculo[]    findAll()
 * @method CriancaVinculo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CriancaVinculoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CriancaVinculo::class);
    }

    // /**
    //  * @return CriancaVinculo[] Returns an array of CriancaVinculo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CriancaVinculo
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

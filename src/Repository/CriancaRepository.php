<?php

namespace App\Repository;

use App\Entity\Crianca;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Crianca|null find($id, $lockMode = null, $lockVersion = null)
 * @method Crianca|null findOneBy(array $criteria, array $orderBy = null)
 * @method Crianca[]    findAll()
 * @method Crianca[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CriancaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Crianca::class);
    }

    // /**
    //  * @return Crianca[] Returns an array of Crianca objects
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
    public function findOneBySomeField($value): ?Crianca
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

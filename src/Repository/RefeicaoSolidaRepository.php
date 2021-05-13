<?php

namespace App\Repository;

use App\Entity\RefeicaoSolida;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RefeicaoSolida|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefeicaoSolida|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefeicaoSolida[]    findAll()
 * @method RefeicaoSolida[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefeicaoSolidaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefeicaoSolida::class);
    }

    // /**
    //  * @return RefeicaoSolida[] Returns an array of RefeicaoSolida objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RefeicaoSolida
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Relatorio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Relatorio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relatorio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relatorio[]    findAll()
 * @method Relatorio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelatorioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relatorio::class);
    }

    // /**
    //  * @return Relatorio[] Returns an array of Relatorio objects
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
    public function findOneBySomeField($value): ?Relatorio
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

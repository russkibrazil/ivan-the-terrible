<?php

namespace App\Repository;

use App\Entity\SeioMaterno;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SeioMaterno|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeioMaterno|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeioMaterno[]    findAll()
 * @method SeioMaterno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeioMaternoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeioMaterno::class);
    }

    // /**
    //  * @return SeioMaterno[] Returns an array of SeioMaterno objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SeioMaterno
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

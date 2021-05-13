<?php

namespace App\Repository;

use App\Entity\Mamadeira;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mamadeira|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mamadeira|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mamadeira[]    findAll()
 * @method Mamadeira[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MamadeiraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mamadeira::class);
    }

    // /**
    //  * @return Mamadeira[] Returns an array of Mamadeira objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mamadeira
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

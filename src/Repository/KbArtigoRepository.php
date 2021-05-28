<?php

namespace App\Repository;

use App\Entity\KbArtigo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KbArtigo|null find($id, $lockMode = null, $lockVersion = null)
 * @method KbArtigo|null findOneBy(array $criteria, array $orderBy = null)
 * @method KbArtigo[]    findAll()
 * @method KbArtigo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KbArtigoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KbArtigo::class);
    }

    /**
     * @param int $limit Quantos artigos serão retornados
     * @return KbArtigo[] Returns an array of KbArtigo objects
     */
    public function getConteudoAleatorio(int $limit = 5)
    {
        $nConteudos = $this->_em->createQuery('SELECT COUNT(c.id) FROM \App\Entity\KbArtigo c')->getResult();
        if ($nConteudos > $limit)
        {
            $valores = [];
            $a = 0;
            do {
                do {
                    $v = rand(1, $nConteudos);
                } while (array_search($v, $valores));
                $valores[] = $v;
            } while (++$a < $limit);

            $q = $this->_em->createQuery('
                SELECT c
                FROM \App\Entity\KbArtigo c
                WHERE c.id IN (:alvos)
            ')
                ->setParameter('alvos', implode(",", $valores))
            ;

            return $q->getResult();
        }
        return $this->findAll();
    }

    /*
    public function findOneBySomeField($value): ?KbArtigo
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

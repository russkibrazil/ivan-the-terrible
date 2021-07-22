<?php

namespace App\Repository;

use App\Entity\Relatorio;
use DateInterval;
use DateTime;
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

    /**
     * Função utilizada para obeter relatórios dentro do intervalo de tempo específicado, tendo uma margem de tolerância de sete dias.
     *
     * @param DateTime $dataInicial
     * @param DateTime $dataFinal
     * @return Relatorio[] Returns an array of Relatorio objects
     */
    public function findBydInicialAnddFinal(DateTime $dataInicial, DateTime $dataFinal)
    {
        /**
         * @var DateInterval $intervaloA Utilizado para adicionar o tempo nas datas
         */
        $intervaloA = new DateInterval('P7D');
        /**
         * @var DateInterval $intervaloS Utilizado para retroceder o tempo para o valor inicial e depois retroceder o período necessário
         */
        $intervaloS = new DateInterval('P14D');

        $q = $this->_em
            ->createQuery('
                SELECT r
                FROM App\Entity\Relatorio r
                WHERE r.dInicio BETWEEN :dimin AND :dimax AND r.dFim BETWEEN :dfmin AND :dfmax
            ')
            ->setParameters([
                'dimax' => strftime('%Y-%m-%d', $dataInicial->add($intervaloA)->getTimestamp()),
                'dimin' => strftime('%Y-%m-%d', $dataInicial->sub($intervaloS)->getTimestamp()),
                'dfmax' => strftime('%Y-%m-%d', $dataFinal->add($intervaloA)->getTimestamp()),
                'dfmin' => strftime('%Y-%m-%d', $dataFinal->sub($intervaloS)->getTimestamp()),
            ]);

        return $q->getResult();
    }

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

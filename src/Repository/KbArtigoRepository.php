<?php

namespace App\Repository;

use App\Entity\KbArtigo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
        $nConteudos = $this->_em->createQuery('SELECT COUNT(c.id) FROM \App\Entity\KbArtigo c')->getScalarResult();
        if ($nConteudos[0][1] > $limit)
        {
            $valores = [];
            $a = 0;
            do {
                do {
                    $v = rand(1, $nConteudos[0][1]);
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

    /**
     * Procedure para buscar artigos contendo as palavras buscadas pelo usuário, restrigo às tags do documento
     *
     * @param string|array $stems lista com as palavras a serem buscadas nas tags
     * @return KbArtigo[]
     */
    public function getArtigosBuscados($stems): array
    {
        if (is_string($stems))
        {
            $palavras = trim($stems, "\t\n\r\0\x0B[]{}()");
            $palavras = str_replace(["'", "\""], '', $palavras);
            $arr_palavras = explode(',', $palavras);
        }
        else
        {
            if (!is_array($stems))
            {
                return null;
            }
            $arr_palavras = $stems;
        }

        $resultados = [];
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('App\Entity\KbArtigo', 't');
        foreach ($arr_palavras as $palavra) {
            $sql = 'SELECT * FROM kb_artigo t WHERE (SELECT JSON_SEARCH(t.tags,"one", "' . $palavra . '%") FROM kb_artigo tt WHERE tt.id = t.id) IS NOT NULL LIMIT 15';
            $q = $this->_em->createNativeQuery($sql, $rsm);
            // https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/native-sql.html
            $resultados = array_merge($resultados, $q->getResult());

        }
        unset($palavra);
        // IDEA Talvez o mais frequente que fosse, mais no topo da pesquisa
        return array_unique($resultados, SORT_REGULAR);
    }
}

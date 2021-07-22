<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    /**
     * @return Usuario|null Returns an Usuario object
     */
    public function findByNomeAndEmail(string $nome, string $email): ?Usuario
    {
        $q = $this->_em->createQuery("
            SELECT u
            FROM App\Entity\Usuario u
            WHERE nome = :nome AND email LIKE :email
        ")
        ->setParameters([
            'nome' => $nome,
            'email' => "{$email}%"
        ])
        ;
        return $q->getOneOrNullResult();
    }

    public function excluirUsuario(Usuario $usuario)
    {
        if (!($this->_em->getRepository(Usuario::class)->find($usuario->getEmail()) instanceof Usuario))
        {
            throw new UnsupportedUserException(sprintf('The provided argument to update e-mails is not valid'));
        }

        $this->_em->createQuery('
            DELETE App\Entity\Medico l
            WHERE l.usuario = :antigo
        ')
        ->setParameter('antigo', $usuario->getEmail())
        ->getResult();

        $this->_em->createQuery('
            DELETE App\Entity\MensagemChat l
            WHERE l.autor = :antigo OR l.destinatario = :antigo
        ')
        ->setParameter('antigo', $usuario->getEmail())
        ->getResult();

        /**
         * @var \App\Entity\CriancaVinculo[] $vinculoPais
         */
        $vinculoPais = $this->_em->createQuery('
            SELECT l FROM App\Entity\CriancaVinculo l
            WHERE l.usuario = :antigo AND (lower(l.parentesco) LIKE "pai" OR lower(l.parentesco) LIKE "m_e")
        ')
        ->setParameter('antigo', $usuario->getEmail())
        ->getResult();

        if (count($vinculoPais) > 0)
        {
            $ids = [];
            /**
             * @var \App\Entity\CriancaVinculo $row
             */
            foreach ($vinculoPais as $row) {
                $ids[] = $row->getCrianca()->getId();
            }
            $ids = implode(",", $ids);

            $this->_em->createQuery('
            DELETE App\Entity\Mamadeira l
            WHERE l.crianca IN (:criancas)
            ')
            ->setParameters([
                'criancas' => $ids,
            ])
            ->getResult();

            $this->_em->createQuery('
            DELETE App\Entity\RefeicaoSolida l
            WHERE l.crianca IN (:criancas)
            ')
            ->setParameters([
                'criancas' => $ids,
            ])
            ->getResult();

            $this->_em->createQuery('
            DELETE App\Entity\SeioMaterno l
            WHERE l.crianca IN (:criancas)
            ')
            ->setParameters([
                'criancas' => $ids,
            ])
            ->getResult();

            $this->_em->createQuery('
            DELETE App\Entity\Relatorio l
            WHERE l.crianca IN (:criancas)
            ')
            ->setParameters([
                'criancas' => $ids,
            ])
            ->getResult();

            $this->_em->createQuery('
            DELETE App\Entity\Crianca l
            WHERE l.id IN (:criancas)
            ')
            ->setParameters([
                'criancas' => $ids,
            ])
            ->getResult();
        }

        /**
         * @var \App\Entity\Relatorio[]|null $auths
         */
        $auths = $this->_em->createQuery('
            SELECT r
            FROM \App\Entity\Relatorio r
            WHERE JSON_SEARCH(r.autorizado,"one", :email) IS NOT NULL
        ')
        ->setParameter('email', $usuario->getEmail())
        ->getResult();

        /**
         * @var \App\Entity\Relatorio $row
         */
        foreach ($auths as &$row) {
            $list_auth = $row->getAutorizado();
            $pos = array_search($usuario, $list_auth);
            unset($list_auth[$pos]);
            $row->setAutorizado($list_auth);
        }
        $this->_em->flush($auths);

        $this->_em->createQuery('
            DELETE App\Entity\CriancaVinculo l
            WHERE l.usuario = :antigo
        ')
        ->setParameter('antigo', $usuario->getEmail())
        ->getResult();

        $this->_em->createQuery('
        DELETE App\Entity\Usuario l
        WHERE l.email = :antigo
        ')
        ->setParameter('antigo', $usuario->getEmail())
        ->getResult();
    }
}

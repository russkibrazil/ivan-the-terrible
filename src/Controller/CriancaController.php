<?php

namespace App\Controller;

use App\Entity\Crianca;
use App\Entity\CriancaVinculo;
use App\Entity\Mamadeira;
use App\Entity\RefeicaoSolida;
use App\Entity\Relatorio;
use App\Entity\SeioMaterno;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Classe controle para rotinas da criança
 * @Route("/crianca")
 */
class CriancaController extends AbstractController
{
    /**
     * Apresentação das crianças vinculadas com o usuário ativo
     *
     * @Route("/", name="crianca_lista")
     * @return Response
     */
    public function index(): Response
    {
        /**
         * @var CriancaVinculo[] $regs
         */
        $regs = $this->getDoctrine()->getRepository(CriancaVinculo::class)->findBy(['usuario' =>  $this->getUser()]);
        $criancas = [];
        /**
         * @var CriancaVinculo $crianca
         */
        foreach ($regs as $crianca) {
            $criancas[] = $crianca->getCrianca();
        }
        return $this->render('crianca/index.html.twig', [
            'lista' => $criancas,
        ]);
    }

    /**
     * Função para pré-processamento e apresentação do resultados dos registros de uma dada criança
     *
     * @Route("/registros", name="crianca_registros", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function registros(Request $request) : Response
    {
        // * O teste aqui deve ver a possibilidade das buscas retornarem nenhum, um ou mais que um registro (array)
        $doctrine = $this->getDoctrine();
        $crianca = $doctrine->getRepository(Crianca::class)->findOneBy(['foto' => $request->cookies->get('cra')]);
        $relatorios = $doctrine->getRepository(Relatorio::class)->findBy(['crianca' => $crianca], ['dh' => 'DESC'], 6);
        $mamadeira = $doctrine->getRepository(Mamadeira::class)->findBy(['crianca' => $crianca], ['dh' => 'DESC'], 10);
        $refeicao = $doctrine->getRepository(RefeicaoSolida::class)->findBy(['crianca' => $crianca], ['dh' => 'DESC'], 10);
        $leitem = $doctrine->getRepository(SeioMaterno::class)->findBy(['crianca' => $crianca], ['dhFim' => 'DESC'], 10);
        $entradas = array_merge($mamadeira, $refeicao, $leitem);
        $i = 0;
        $elementos = count($entradas);
        do {
            $redo = false;
            if ($entradas[$i]->getDh()->getTimestamp() < $entradas[$i + 1]->getDh()->getTimestamp())
            {
                $temp = $entradas[$i + 1];
                $entradas[$i+1] = $entradas[$i];
                $entradas[$i] = $temp;
                $redo = true;
            }
            if (++$i == $elementos)
            {
                $i = 0;
            }
        } while ($redo);
        unset($i, $elementos, $temp);
        return $this->render('crianca/registros.html.twig', [
            'relatorios' => $relatorios,
            'entradas' => array_slice($entradas, 0, 15),
        ]);
    }

    /**
     * Rota para inclusão de uma nova crinça vinculada ao usuário ativo
     *
     * @Route("/novo", name="crianca_novo", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function incluir(Request $request) : Response
    {
        $dados = new Crianca();
        $form = $this->createForm(Crianca::class, $dados);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $vinculo = new CriancaVinculo();
            $vinculo->setCrianca($dados);
            $vinculo->setUsuario($this->getUser());
            $vinculo->setParentesco($form->get('parentesco')->getData());
            $mgr = $this->getDoctrine()->getManager();
            $mgr->persist($dados);
            $mgr->flush();
            $this->addFlash('sucesso', 'Criança criada');
        }
        return $this->render('crianca/novo.html.twig', [
            'form' => $form->createView(),
        ]);
    }



}

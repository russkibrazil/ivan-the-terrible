<?php

namespace App\Controller;

use App\Entity\Crianca;
use App\Entity\CriancaVinculo;
use App\Entity\Mamadeira;
use App\Entity\RefeicaoSolida;
use App\Entity\Relatorio;
use App\Entity\SeioMaterno;
use App\Form\CriancaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Classe controle para rotinas da criança
 * @Route("/crianca")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
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
        if ($regs)
        {
            $criancas = [];
            /**
             * @var CriancaVinculo $crianca
             */
            foreach ($regs as $crianca) {
                $criancas[] = $crianca->getCrianca();
            }
            return $this->render('crianca/index.html.twig', [
                'criancas' => $criancas,
            ]);
        }
        else
        {
            return $this->render('crianca/nenhum_vinculo.html.twig', []);
        }
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
        $crianca = $doctrine->getRepository(Crianca::class)->find(strtok(urldecode($request->cookies->get('cra')),','));
        if ($crianca)
        {
            $relatorios = $doctrine->getRepository(Relatorio::class)->findBy(['crianca' => $crianca], ['dh' => 'DESC'], 6);
            $mamadeira = $doctrine->getRepository(Mamadeira::class)->findBy(['crianca' => $crianca], ['dh' => 'DESC'], 10);
            $refeicao = $doctrine->getRepository(RefeicaoSolida::class)->findBy(['crianca' => $crianca], ['dh' => 'DESC'], 10);
            $leitem = $doctrine->getRepository(SeioMaterno::class)->findBy(['crianca' => $crianca], ['dhFim' => 'DESC'], 10);
            $entradas = array_merge($mamadeira, $refeicao, $leitem);

            $redo = true;
            for ($i=0; $i < count($entradas)-1 && $redo; $i++) {
                $redo = false;
                for ($j=0; $j < count($entradas)-$i-1; $j++) {
                    if ($entradas[$j]->getDh()->getTimestamp() < $entradas[$j+1]->getDh()->getTimestamp())            {
                        $redo = true;
                        $temp = $entradas[$j];
                        $entradas[$j] = $entradas[$j+1];
                        $entradas[$j+1] = $temp;
                    }
                }
            }
            unset($i, $j, $elementos, $temp);
            return $this->render('crianca/registros.html.twig', [
                'relatorios' => $relatorios,
                'entradas' => array_slice($entradas, 0, 15),
            ]);
        }
        else
        {
            return $this->render('crianca/nenhum_vinculo.html.twig', []);
        }
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
        $form = $this->createForm(CriancaType::class, $dados);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var \App\Entity\Usuario $u
             */
            $u = $this->getUser();
            $vinculo = new CriancaVinculo();
            $vinculo->setCrianca($dados);
            $vinculo->setUsuario($u);
            $vinculo->setParentesco($form->get('parentesco')->getData());
            if ($dados->getNomeFoto() == null)
            {
                $dados->setNomeFoto(strtolower($dados->getNome()[0]) . '.png');
            }

            $mgr = $this->getDoctrine()->getManager();
            $mgr->persist($dados);
            $mgr->persist($vinculo);
            $mgr->flush();
            $this->addFlash('sucesso', 'Criança criada');

            $recentes = $u->getCriancaRecentes();
            if ($recentes == null)
            {
                $recentes = [];
            }
            array_unshift($recentes, [$dados->getId() => $dados->getNomeFoto()]);
            unset($recentes[5]);
            $u->setCriancaRecentes($recentes);
            $mgr->flush();

            $cra = array_shift($recentes);
            $strOut = '';
            foreach ($recentes as $row) {
                $strOut .= array_keys($row[0]) . ',' . array_values($row[0]) . '|';
            }
            /**
             * @var \Symfony\Component\HttpFoundation\RedirectResponse $response
             */
            $response = $this->redirectToRoute('crianca_lista');
            $response->headers->setCookie(Cookie::create('cra', array_keys($cra)[0] . ',' . array_values($cra)[0], 0, '/',null, null, false));
            $response->headers->setCookie(Cookie::create('cr', $strOut, 0, '/', null, null, false));

            return $response;
        }
        return $this->render('crianca/novo.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

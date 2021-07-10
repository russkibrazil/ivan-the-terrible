<?php

namespace App\Controller;

use App\Entity\Crianca;
use App\Entity\Relatorio;
use App\Form\IntervaloBuscaType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller para rotas referentes aos relatórios e atividades afins
 * @Route("/relatorio")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class RelatorioController extends AbstractController
{
    /**
     * @Route("/", name="relatorio")
     */
    public function index(): Response
    {
        return $this->render('relatorio/index.html.twig', [
            'controller_name' => 'RelatorioController',
        ]);
    }

    /**
     * Função que permite o médico selecionar qual o intervalo de tempo que ele deseja que um relatório seja construído, ou então selecionar um existente, caso o intervalo selecionado coincida com algum dos critérios
     *
     * @Route("/requerir", name="relatorio_requerir", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function requerirRelatorio(Request $request) : Response
    {
        $form = $this->createForm(IntervaloBuscaType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $doctrine = $this->getDoctrine();
            $inicio = date_create_from_format('d/m/Y', $request->request->get('dInicio')->getData());
            $fim = date_create_from_format('d/m/Y', $request->request->get('dFim')->getData());
            $intervalo = date_diff($inicio, $fim, true);
            /**
             * @var \App\Repository\RelatorioRepository $rrepo
             */
            $rrepo = $doctrine->getRepository(Relatorio::class);
            $rels = $rrepo->findBydInicialAnddFinal($inicio, $fim);
            $crianca = $doctrine->getRepository(Crianca::class)->findOneBy(['foto' => $request->cookies->get('cra')]);
            if (count($rels) == 0 || $rels == false)
            {
                $this->addFlash('sucesso', 'Pedido de relatório incluído. Aguarde autorização dos pais para a geração do documento.');
                $this->geracaoPedidoRelatorio($inicio, $fim, $crianca);
                //TODO Redirecionar para...?
                return $this->redirectToRoute('home');
            }
            else
            {
                /**
                 * @var Relatorio $exact Armazena o relatório que tem exatamente a mesma data inicial e final de análise dos dados
                 */
                $exact = null;
                /**
                 * @var Relatorio[] $oneHit Armazena os relatórios que a data inicial ou a final da análise coincide com a buscada pelo médico
                 */
                $oneHit = [];
                /**
                 * @var Relatorio[] $period Armazena os relatorios que coincidem no período, com a margem de erro de 3 dias
                 */
                $period = [];
                foreach ($rels as $row) {
                    // * Separar os relatórios por: (a)As duas datas coincidem (b) Uma das datas coincide (c) O período coincide (erro de 3 dias)
                    if ($row->getDInicio()->getTimestamp() == $inicio->getTimestamp() && $row->getDFim()->getTimestamp() == $fim->getTimestamp())
                    {
                        $exact = $row;
                        continue;
                    }
                    elseif ($row->getDInicio()->getTimestamp() == $inicio->getTimestamp() || $row->getDFim()->getTimestamp() == $fim->getTimestamp())
                    {
                        $oneHit[] = $row;
                        continue;
                    }
                    $rowIntervalo = date_diff($row->getDInicio(), $row->getDFim(), true);
                    if ($rowIntervalo->days >= ($intervalo->days - 3) && $rowIntervalo->days <= ($intervalo->days + 3))
                    {
                        $period[] = $row;
                        continue;
                    }
                }
                $response = $this->render('relatorio/selecionar_relatorio_candidato.html.twig', [
                    'exact' => $exact,
                    'oneHit' => $oneHit,
                    'period' => $period,
                ]);
                $response->headers->setCookie(new Cookie('dataReq', "{$inicio->getTimestamp()}|{$fim->getTimestamp()}"));
                return $response;
            }
        }
        return $this->render('relatorio/selecionar_data.html.twig', []);
    }
// relatorio_acesso
    public function requerirAcesso(Request $request) : JsonResponse
    {
        /**
         * @var Relatorio|null $relatorio
         */
        $relatorio = $this->getDoctrine()->getRepository(Relatorio::class)->find($request->request->getInt('relatorio'));
        if ($relatorio === null)
            return new JsonResponse(null, 404);
        // TODO Criar modo requisição do acesso
        return new JsonResponse();
    }

    //relatorio_forcar_requisicao
    public function pedidoRelatorio(Request $request) : JsonResponse
    {
        $data = explode($request->cookies->get('dataReq'), '|');
        $doctrine = $this->getDoctrine();
        $crianca = $doctrine->getRepository(Crianca::class)->findOneBy(['foto' => $request->cookies->get('cra')]);
        $this->geracaoPedidoRelatorio(new DateTime($data[0]), new DateTime($data[1]), $crianca);
        $request->cookies->remove('dataReq');
        return new JsonResponse();
    }

    private function geracaoPedidoRelatorio(DateTime $inicio, DateTime $fim, Crianca $crianca)
    {
        $rel = (new Relatorio())
            ->setDInicio($inicio)
            ->setDFim($fim)
            ->setCrianca($crianca)
            ->setDh(new DateTime());
        $mgr = $this->getDoctrine()->getManager();
        $mgr->persist($rel);
        $mgr->flush();
        // TODO Enviar trabalho para o message broker
    }
}

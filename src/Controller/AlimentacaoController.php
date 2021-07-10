<?php

namespace App\Controller;

use App\Entity\Crianca;
use App\Entity\Mamadeira;
use App\Entity\RefeicaoSolida;
use App\Entity\SeioMaterno;
use App\Form\MamadeiraType;
use App\Form\RefeicaoSolidaType;
use DateTime;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Classe Controle para as entradas de alimentação
 * @Route("/alimentecao")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class AlimentacaoController extends AbstractController
{
    /**
     * @Route("/", name="alimentacao")
     */
    public function index(): Response
    {
        return $this->render('alimentacao/index.html.twig', [
            'controller_name' => 'AlimentacaoController',
        ]);
    }

    /**
     * Rota para registro de amamentação'
     *
     * @Route("/amamentar", name="amamentacao", methods={"GET"})
     * @return Response
     */
    public function amamentacao()
    {
        return $this->render('alimentacao/amamentacao.html.twig', []);
    }

    /**
     * Rota POST das entradas de amamentação
     * @Route("/amamentar", name="amamentacao_post", methods={"POST"})
     *
     * @param Request $request
     * @var array $entradas
     * @var array $list_persist
     * @var string $ultimaCrianca
     * @return JsonResponse
     */
    public function amamentacaoPost(Request $request)
    {
        // $loader = new AnnotationLoader(new AnnotationReader());
        // $norm = new ObjectNormalizer(new ClassMetadataFactory($loader));
        // $serial = new Serializer([$norm],[]);
        $entradas = $request->request->get('estados');
        $list_persist = [];
        $log = new SeioMaterno();
        $mgr = $this->getDoctrine()->getManager();
        $repoCrianca = $this->getDoctrine()->getRepository(Crianca::class);
        $ultimaCrianca = null;
        $i = 0;
        foreach ($entradas as $item) {
            $log->setLado($item['lado']);
            $log->setDhInicio(new DateTime($item['dhInicio']));
            try
            {
                $log->setDhFim(new DateTime($item['dhFim']));
            }
            catch (Exception $e) {
                $log->setDhFim(new DateTime('now'));
            }
            if ($item['crianca'] != $ultimaCrianca)
            {
                $crianca = $repoCrianca->findOneBy(['foto' => $item['crianca']]);
                $ultimaCrianca =$item['crianca'];
            }
            $log->setCrianca($crianca);
            $list_persist[] = $log;
            $mgr->persist($list_persist[$i++]);
        }
        $mgr->flush();
        unset($item);
        return new JsonResponse();
    }

    /**
     * Rota para registro de alimentação'
     *
     * @Route("/bebida", name="bebida", methods={"GET", "POST"})
     * @param Request $request
     * @var FormInterface $form
     * @return Response
     */
    public function alimentacao(Request $request)
    {
        $dados = new Mamadeira();
        $form = $this->createForm(MamadeiraType::class, $dados);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $dados->setDh(new DateTime());
            $om = $this->getDoctrine()->getManager();
            $om->persist($dados);
            $om->flush();
            $this->addFlash('sucesso', 'Registro incluído');
        }
        return $this->render('alimentacao/bebida.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Rota para registro de refeição sólida'
     *
     * @Route("/alimentar", name="refeicao", methods={"GET", "POST"})
     * @param Request $request
     * @var FormInterface $form
     * @return Response
     */
    public function refeicao(Request $request)
    {
        $dados = new RefeicaoSolida();
        $form = $this->createForm(RefeicaoSolidaType::class, $dados);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $dados->setDh(new DateTime());
            $om = $this->getDoctrine()->getManager();
            $om->persist($dados);
            $om->flush();
            $this->addFlash('sucesso', 'Registro incluído');
        }
        return $this->render('alimentacao/alimentacao.html.twig', []);
    }
}

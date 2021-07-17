<?php

namespace App\Controller;

use App\Entity\Crianca;
use App\Entity\CriancaVinculo;
use App\Entity\Usuario;
use App\Form\AlterarSenhaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller para ações envolvendo os usuários da plataforma, de maneira geral. Ações específicas de médicos não estão aqui.
 * @Route("/pessoa")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class PessoaController extends AbstractController
{
    /**
     * @Route("/", name="pessoa")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('pessoa_perfil');
    }

    /**
     * Rota para apresentar perfil
     * @Route("/perfil", name="pessoa_perfil", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function perfil(Request $request): Response
    {
        /**
         * @var Usuario $u
         */
        $u = $this->getUser();
        $vinculos = $this->getDoctrine()->getRepository(CriancaVinculo::class)->findBy(['usuario' => $u->getEmail()], null, 25);
        return $this->render('pessoa/perfil.html.twig', [
            'usuario' => $u,
            'vinculos' => $vinculos,
        ]);
    }

    /**
     * Rota para processar a exclusão do usuário ativo
     * @Route("/apagar", name="pessoa_apagar_conta", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function apagarConta(Request $request): Response
    {
        $u = $this->getUser();
        $form = $this->createForm(ApagarUserType::class, null, [
            'email' => $u->getEmail()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var \App\Repository\UsuarioRepository $repo
             */
            $repo = $this->getDoctrine()->getRepository(User::class);
            $repo->excluirUsuario($u);
        }
        return $this->render('pessoa/apagar_conta.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Rota para alterar a senha do usuário
     * @Route("/nova-senha", name="pessoa_alterar_senha", methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function alterarSenha(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(AlterarSenhaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $u = $this->getUser();

            $encodedPassword = $passwordEncoder->encodePassword(
                $u,
                $form->get('novaSenha')->getData()
            );

            $u->setSenha($encodedPassword);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('sucesso', 'Senha alterada com sucesso.');
            return $this->redirectToRoute('pessoa_perfil');
        }

        return $this->render('pessoa/alterar_senha.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Rota para buscar pessoa para vincular a uma criança
     * @Route("/vincular", name="pessoa_vincular", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function vincular(Request $request): Response
    {
        return $this->render('pessoa/vincular.html.twig', []);
    }

    /**
     * Rota POST para buscar usuários
     * @Route("/perfil/buscar", name="pessoa_buscar_ajax", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function buscaPerfil(Request $request): JsonResponse
    {
        $pesquisa = $request->cookies->get('pesquisa');
        $filtro = [];
        if (stripos($pesquisa, '@'))
        {
            $filtro = ['email' => $pesquisa];
        }
        else
        {
            $filtro = ['nome' => $pesquisa];
        }
        /**
         * @var Usuario[] $resultado
         */
        $resultado = $this->getDoctrine()->getRepository(Usuario::class)->findBy($filtro);
        if (count($resultado) > 0)
        {
            $loader = new AnnotationLoader(new AnnotationReader());
            $norm = new ObjectNormalizer(new ClassMetadataFactory($loader));
            $serial = new Serializer([$norm],[new JsonEncoder()]);
            $arr = $serial->serialize($resultado, 'json',
                [
                    'groups' => 'busca_usuario',
                    AbstractNormalizer::CALLBACKS => [
                        'email' => function($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
                            $tamanho = strlen($innerObject);
                            return substr_replace($innerObject, '***************', $tamanho/2);
                        }
                    ]
                ]
            ); // * Testar Ocultar parcialmente o e-mail usando callback
            return new JsonResponse($arr);
        }
        else
        {
            return new JsonResponse(null, 404);
        }
    }

    /**
     * Rota POST para criar o vinculo da crinça com o usuário selecionado
     *
     * @Route("/vincular/novo", name="pessoa_vincular_novo_ajax", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function criarVinculo(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $doctrine = $this->getDoctrine();
        /**
         * @var \App\Repository\UsuarioRepository $usuRepo
         */
        $usuRepo = $doctrine->getRepository(Usuario::class);
        $usuario = $usuRepo->findByNomeAndEmail( $request->request->get('nome'), strstr($request->request->get('email'), '*', true));
        $crianca = $doctrine->getRepository(Crianca::class)->findOneBy(['nomeFoto' => $request->cookies->get('cra')]);
        $vinculo = (new CriancaVinculo())
            ->setCrianca($crianca)
            ->setUsuario($usuario)
            ->setParentesco($request->request->get('parentesco'))
        ;
        $erros = $validator->validate($vinculo);
        if (count($erros) <= 0)
        {
            $mgr = $doctrine->getManager();
            $mgr->persist($vinculo);
            $mgr->flush();
            return new JsonResponse();
        }
        else
        {
            return new JsonResponse("{'erros': {(string) $erros}", 400);
        }
    }

    /**
     * Rota para atualizar a lista de crianças no perfil do usuário
     * @Route("/criancas-recentes", name="pessoa_atualizar_criancas_ajax", methods={"POST"})
     *
     * @param Request $request
     * @return void
     */
    public function atualizarCriancasRecentes(Request $request)
    {
        /**
         * @var \App\Entity\Usuario $u
         */
        $u = $this->getUser();
        $u->setCriancaRecentes($request->request->get('criancas'));
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }
}

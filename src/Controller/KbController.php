<?php

namespace App\Controller;

use App\Entity\KbArtigo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller para apresentação dos conteúdos inseridos na plataforma, sejam explanativos, informativos ou autorais, que possam ser de ajuda/interesse dos pais
 * @Route("/kb")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class KbController extends AbstractController
// Neste Controller, vejamos a disputa entre armazenar o HTML as-is ou como Mrkdown e converte-lo no Symfony para HTML e (talvez) utilizar o Stymulus para transmitir os resultados, em qualquer dos casos
// https://stimulus.hotwire.dev/handbook/introduction
// https://michelf.ca/projects/php-markdown/
{
    /**
     * @Route("/", name="kb_busca")
     */
    public function index(): Response
    {
        /**
         * @var \App\Repository\KbArtigoRepository $repo
         */
        $repo = $this->getDoctrine()->getRepository(KbArtigo::class);

        $recentes = $repo->getConteudoAleatorio();
        return $this->render('kb/index.html.twig', [
            'artigos' => $recentes,
        ]);
    }

    /**
     * Apresentação do conteúdo buscado
     * @Route("/{$slug}", name="kb_mostrar_conteudo", methods={"GET"})
     * @return Response
     */
    public function show($slug): Response
    {
        /**
         * @var \App\Entity\KbArtigo|null $art
         */
        $art = $this->getDoctrine()->getRepository(KbArtigo::class)->findOneBy(['slug' => $slug]);
        if ($art === null)
        {
            // CHECK É necessário personalização ou a página padrão do Twig será utilizada?
            return new Response('', 404);
        }
        // TODO Instalar conversor MD=>HTML
        return $this->render('kb/show.html.twig', [
            'titulo' => $art->getTitulo(),
            'conteudo' => $art->getCorpo(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\KbArtigo;
use App\Form\KbBuscaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

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
    public function index(Request $request): Response
    {
        $form = $this->createForm(KbBuscaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $artigos = $this->buscarConteudo($form->get('busca')->getData());
        }
        else
        {
            /**
             * @var \App\Repository\KbArtigoRepository $repo
             */
            $repo = $this->getDoctrine()->getRepository(KbArtigo::class);

            $artigos = $repo->getConteudoAleatorio();
        }

        return $this->render('kb/index.html.twig', [
            'artigos' => $artigos,
            'form' => $form->createView()
        ]);
    }

    /**
     * Apresentação do conteúdo buscado
     * @Route("/{slug}", name="kb_mostrar_conteudo", methods={"GET"})
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
        return $this->render('kb/show.html.twig', [
            'titulo' => $art->getTitulo(),
            'conteudo' => $art->getCorpo(),
        ]);
    }

    private function buscarConteudo(string $query)
    {
        # https://stackoverflow.com/questions/19735250/running-a-python-script-from-php
        // IDEA Oportunidade de implementar Stimulus
        $comando = escapeshellcmd('/home/igor/anaconda3/bin/python "' . __DIR__ . '/../../search_content/search.py" ' . $query);
        $res = exec($comando);
        /**
         * @var \App\Repository\KbArtigoRepository $kbRepo
         */
        $kbRepo = $this->getDoctrine()->getRepository(KbArtigo::class);
        $artigos = $kbRepo->getArtigosBuscados($res);

        return $artigos;
    }
}

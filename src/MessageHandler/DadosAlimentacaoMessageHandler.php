<?php

namespace App\MessageHandler;

use App\Message\DadosAlimentacaoMessage;
use App\Repository\CriancaRepository;
use App\Repository\RelatorioRepository;
use DateTime;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;

final class DadosAlimentacaoMessageHandler implements MessageHandlerInterface
{
    private $pdfGen;
    private $objectManager;
    private $relRepository;
    private $criRepository;

    public function __construct(RelatorioRepository $relatorioRepository, CriancaRepository $criancaRepository, EntityManagerInterface $entityManagerInterface, Pdf $pdf)
    {
        $this->pdfGen = $pdf;
        $this->objectManager = $entityManagerInterface;
        $this->relRepository = $relatorioRepository;
        $this->criRepository = $criancaRepository;
    }

    public function __invoke(DadosAlimentacaoMessage $message)
    {
        $comando = escapeshellcmd('/home/igor/anaconda3/bin/python "' . __DIR__ . '/../../data_process/process.py" ' . $message->getCrianca() . ' ' . $message->getDataInicio() . ' ' .$message->getDataFim());
        shell_exec($comando);
        $prefix = $message->getCrianca() . '-' . $message->getDataInicio() . '-' . $message->getDataFim() . '-';
        $amaVolume = $this->getCaminhoRecurso($prefix . 'leiteMaterno.png');
        $amaScatter = $this->getCaminhoRecurso($prefix . 'scatterLeiteMaterno.png');
        $liqVolume = $this->getCaminhoRecurso($prefix . 'somaMamadeira.png');
        $tabSolidos = $this->getCaminhoRecurso($prefix . 'tabelaSolidos.json');
        $arrSolidos = null;

        if (is_string($tabSolidos))
        {
            $stream = fopen($tabSolidos, 'r');
            $arrSolidos = json_decode(fread($stream, filesize($tabSolidos)), true);
            fclose($stream);
        }

        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__,2).'/templates');
        $twig = new \Twig\Environment($loader, [
            'charset' => 'utf-8',
            'cache' => false,
        ]);
        $nomeArq = uniqid("rel", true);
        $pathRel = 'public/doc/relatorio/' . substr($nomeArq,0,4) . '/' . substr($nomeArq,5,4) . '/' . $nomeArq . '.pdf';
        $this->pdfGen->generateFromHtml(
            $twig->render('relatorio/relatorio.html.twig', [
                'css' => dirname(__DIR__,2) . '/node_modules/bootstrap/dist/css/bootstrap.min.css',
                'crianca' => $this->criRepository->find($message->getCrianca()),
                'dataInicio' => $message->getDataInicio(),
                'dataFim' => $message->getDataFim(),
                'amamentacao_barras' => $amaVolume,
                'amamentacao_scatter' => $amaScatter,
                'grafico_liquidos' => $liqVolume,
                'solidos' => $arrSolidos,
            ]),
            $pathRel,
            []
        );
        $relatorio = $this->relRepository->findOneBy([
            'crianca' => $message->getCrianca(),
            'dInicio' => new DateTime($message->getDataInicio()),
            'dFim' => new DateTime($message->getDataFim()),
        ]);
        $relatorio->setNomeArquivo($nomeArq.'.pdf');
        $this->objectManager->flush();
        unlink($amaVolume);
        unlink($amaScatter);
        unlink($liqVolume);
        unlink($tabSolidos);
    }

    private function getCaminhoRecurso(string $nomeRecurso)
    {
        $p = __DIR__ . '/../../data_process/export/' . $nomeRecurso;
        if (file_exists($p))
            return $p;
        return null;
    }
}

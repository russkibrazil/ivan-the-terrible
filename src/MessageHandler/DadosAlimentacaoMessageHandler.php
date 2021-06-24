<?php

namespace App\MessageHandler;

use App\Message\DadosAlimentacaoMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DadosAlimentacaoMessageHandler implements MessageHandlerInterface
{
    const PATH_FILA_DADOS = './data_process/processados';
    public function __invoke(DadosAlimentacaoMessage $message)
    {
        $filaDados = fopen(self::PATH_FILA_DADOS, 'a');
        fwrite($filaDados, "{$message->getCrianca()}|{$message->getDataInicio()}|{$message->getDataFim()}|{$message->getAutorizados()}");
        fclose($filaDados);
    }
}

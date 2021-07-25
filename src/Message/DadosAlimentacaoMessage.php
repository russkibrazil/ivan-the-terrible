<?php

namespace App\Message;

final class DadosAlimentacaoMessage
{
    private $crianca;
    private $dataInicio;
    private $dataFim;
    private $autorizados;

    public function __construct(int $crianca, string $dataInicio, string $dataFim, array $autorizados = [])
    {
        $this->crianca = $crianca;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->autorizados = $autorizados;
    }

    public function getCrianca(): int
    {
        return $this->crianca;
    }

    public function getDataInicio(): string
    {
        return $this->dataInicio;
    }

    public function getDataFim(): string
    {
        return $this->dataFim;
    }

    public function getAutorizados(): string
    {
        return implode(',', $this->autorizados);
    }
}

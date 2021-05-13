<?php

namespace App\Entity;

use App\Repository\RelatorioRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RelatorioRepository::class)
 */
class Relatorio
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Crianca::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $crianca;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $autorizado = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCrianca(): ?Crianca
    {
        return $this->crianca;
    }

    public function setCrianca(?Crianca $crianca): self
    {
        $this->crianca = $crianca;

        return $this;
    }

    public function getAutorizado(): ?array
    {
        return $this->autorizado;
    }

    public function setAutorizado(?array $autorizado): self
    {
        $this->autorizado = $autorizado;

        return $this;
    }
}

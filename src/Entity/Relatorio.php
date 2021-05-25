<?php

namespace App\Entity;

use App\Repository\RelatorioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $dh;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     */
    private $dInicio;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     */
    private $dFim;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nomeArquivo;

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

    public function getDh(): ?\DateTimeInterface
    {
        return $this->dh;
    }

    public function setDh(\DateTimeInterface $dh): self
    {
        $this->dh = $dh;

        return $this;
    }

    public function getDInicio(): ?\DateTimeInterface
    {
        return $this->dInicio;
    }

    public function setDInicio(\DateTimeInterface $dInicio): self
    {
        $this->dInicio = $dInicio;

        return $this;
    }

    public function getDFim(): ?\DateTimeInterface
    {
        return $this->dFim;
    }

    public function setDFim(\DateTimeInterface $dFim): self
    {
        $this->dFim = $dFim;

        return $this;
    }

    public function getNomeArquivo() : string
    {
        return $this->nomeArquivo;
    }

    public function setNomeArquivo(string $nomeArquivo) : self
    {
        $this->nomeArquivo = $nomeArquivo;

        return $this;
    }
}

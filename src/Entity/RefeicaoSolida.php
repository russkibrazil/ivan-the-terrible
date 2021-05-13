<?php

namespace App\Entity;

use App\Repository\RefeicaoSolidaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RefeicaoSolidaRepository::class)
 */
class RefeicaoSolida
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dh;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $volume;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $anotacao;

    /**
     * @ORM\ManyToOne(targetEntity=Crianca::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $crianca;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getAnotacao(): ?string
    {
        return $this->anotacao;
    }

    public function setAnotacao(?string $anotacao): self
    {
        $this->anotacao = $anotacao;

        return $this;
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
}

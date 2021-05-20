<?php

namespace App\Entity;

use App\Repository\RefeicaoSolidaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     */
    private $dh;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(message="A quantidade deve ser um valor maior que zero")
     */
    private $volume;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $anotacao;

    /**
     * @ORM\ManyToOne(targetEntity=Crianca::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
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

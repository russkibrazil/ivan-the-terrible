<?php

namespace App\Entity;

use App\Repository\MamadeiraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MamadeiraRepository::class)
 */
class Mamadeira
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
     * @ORM\Column(type="string", length=20)
     */
    private $alimento;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $volume;

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

    public function getAlimento(): ?string
    {
        return $this->alimento;
    }

    public function setAlimento(string $alimento): self
    {
        $this->alimento = $alimento;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;

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

<?php

namespace App\Entity;

use App\Repository\SeioMaternoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeioMaternoRepository::class)
 */
class SeioMaterno
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
    private $dhInicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dhFim;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $lado;

    /**
     * @ORM\ManyToOne(targetEntity=Crianca::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $crianca;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDhInicio(): ?\DateTimeInterface
    {
        return $this->dhInicio;
    }

    public function setDhInicio(\DateTimeInterface $dhInicio): self
    {
        $this->dhInicio = $dhInicio;

        return $this;
    }

    public function getDhFim(): ?\DateTimeInterface
    {
        return $this->dhFim;
    }

    public function setDhFim(\DateTimeInterface $dhFim): self
    {
        $this->dhFim = $dhFim;

        return $this;
    }

    public function getLado(): ?string
    {
        return $this->lado;
    }

    public function setLado(string $lado): self
    {
        $this->lado = $lado;

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

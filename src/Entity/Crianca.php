<?php

namespace App\Entity;

use App\Repository\CriancaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CriancaRepository::class)
 */
class Crianca
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=100, minMessage="Digite no mínimo o primeiro nome", maxMessage="Nome muito extenso")
     */
    private $nome;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     */
    private $dn;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $foto;

    /**
     * @ORM\OneToMany(targetEntity=CriancaVinculo::class, mappedBy="crianca", orphanRemoval=true)
     */
    private $criancaVinculos;

    public function __construct()
    {
        $this->criancaVinculos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDn(): ?\DateTimeInterface
    {
        return $this->dn;
    }

    public function setDn(\DateTimeInterface $dn): self
    {
        $this->dn = $dn;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * @return Collection|CriancaVinculo[]
     */
    public function getCriancaVinculos(): Collection
    {
        return $this->criancaVinculos;
    }

    public function addCriancaVinculo(CriancaVinculo $criancaVinculo): self
    {
        if (!$this->criancaVinculos->contains($criancaVinculo)) {
            $this->criancaVinculos[] = $criancaVinculo;
            $criancaVinculo->setCrianca($this);
        }

        return $this;
    }

    public function removeCriancaVinculo(CriancaVinculo $criancaVinculo): self
    {
        if ($this->criancaVinculos->removeElement($criancaVinculo)) {
            // set the owning side to null (unless already changed)
            if ($criancaVinculo->getCrianca() === $this) {
                $criancaVinculo->setCrianca(null);
            }
        }

        return $this;
    }
}

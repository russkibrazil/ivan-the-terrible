<?php

namespace App\Entity;

use App\Repository\MedicoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MedicoRepository::class)
 */
class Medico
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $especialidade;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $crm;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $ufCrm;

    /**
     * @ORM\Column(type="date")
     */
    private $validade;

    /**
     * @ORM\OneToOne(targetEntity=Usuario::class, inversedBy="registroMedico", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEspecialidade(): ?string
    {
        return $this->especialidade;
    }

    public function setEspecialidade(string $especialidade): self
    {
        $this->especialidade = $especialidade;

        return $this;
    }

    public function getCrm(): ?string
    {
        return $this->crm;
    }

    public function setCrm(string $crm): self
    {
        $this->crm = $crm;

        return $this;
    }

    public function getUfCrm(): ?string
    {
        return $this->ufCrm;
    }

    public function setUfCrm(string $ufCrm): self
    {
        $this->ufCrm = $ufCrm;

        return $this;
    }

    public function getValidade(): ?\DateTimeInterface
    {
        return $this->validade;
    }

    public function setValidade(\DateTimeInterface $validade): self
    {
        $this->validade = $validade;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}

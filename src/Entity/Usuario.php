<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 */
class Usuario
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $senha;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $Nome;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $foto;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataCadastro;

    /**
     * @ORM\OneToMany(targetEntity=CriancaVinculo::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $criancaVinculos;

    /**
     * @ORM\OneToOne(targetEntity=Medico::class, mappedBy="usuario", cascade={"persist", "remove"})
     */
    private $registroMedico;

    public function __construct()
    {
        $this->criancaVinculos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSenha(): ?string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->Nome;
    }

    public function setNome(string $Nome): self
    {
        $this->Nome = $Nome;

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

    public function getDataCadastro(): ?\DateTimeInterface
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro(?\DateTimeInterface $dataCadastro): self
    {
        $this->dataCadastro = $dataCadastro;

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
            $criancaVinculo->setUsuario($this);
        }

        return $this;
    }

    public function removeCriancaVinculo(CriancaVinculo $criancaVinculo): self
    {
        if ($this->criancaVinculos->removeElement($criancaVinculo)) {
            // set the owning side to null (unless already changed)
            if ($criancaVinculo->getUsuario() === $this) {
                $criancaVinculo->setUsuario(null);
            }
        }

        return $this;
    }

    public function getRegistroMedico(): ?Medico
    {
        return $this->registroMedico;
    }

    public function setRegistroMedico(Medico $registroMedico): self
    {
        // set the owning side of the relation if necessary
        if ($registroMedico->getUsuario() !== $this) {
            $registroMedico->setUsuario($this);
        }

        $this->registroMedico = $registroMedico;

        return $this;
    }
}

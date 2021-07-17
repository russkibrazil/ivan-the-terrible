<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 * @UniqueEntity("email")
 * @Vich\Uploadable
 */
class Usuario implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\Email(message = "Use um endereço de e-mail válido")
     * @Assert\NotBlank
     * @Assert\Length(max=100, maxMessage="Utilize um endereço de e-mail mais curto")
     * @Groups({"busca_usuario"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank
     * @Assert\Length(max=180, maxMessage="Utilize uma senha mais curta")
     */
    private $senha;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(min=10, max=100, minMessage="Digite nome e sobrenome", maxMessage="Nome muito longo")
     * @Assert\NotBlank
     * @Groups({"busca_usuario"})
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"busca_usuario"})
     */
    private $nomeFoto;

    /**
     * Foto recuperado pelo Vich
     *
     * @var File|null
     * @Vich\UploadableField(mapping="pessoa", fileNameProperty="nomeFoto")
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

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $criancaRecentes = [];

    public function __construct()
    {
        $this->criancaVinculos = new ArrayCollection();
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
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getNomeFoto(): ?string
    {
        return $this->nomeFoto;
    }

    public function setNomeFoto(?string $nomeFoto): self
    {
        $this->nomeFoto = $nomeFoto;

        return $this;
    }

    public function setFoto(?File $foto = null): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getFoto(): ?File
    {
        return $this->foto;
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

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->senha;
    }
    public function getSalt()
    {
    }
    public function eraseCredentials()
    {
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCriancaRecentes(): ?array
    {
        return $this->criancaRecentes;
    }

    public function setCriancaRecentes(?array $criancaRecentes): self
    {
        $this->criancaRecentes = $criancaRecentes;

        return $this;
    }
}

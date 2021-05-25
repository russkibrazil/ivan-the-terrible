<?php

namespace App\Entity;

use App\Repository\CriancaVinculoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CriancaVinculoRepository::class)
 * @UniqueEntity(fields={"usuario", "crianca"}, errorPath="usuario", message="Essa criança já tem esta pessoa vinculada")
 */
class CriancaVinculo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="criancaVinculos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Crianca::class, inversedBy="criancaVinculos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $crianca;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=10,
     *      minMessage="Defina o parentesco usando ao menos {{ limit }} caracteres",
     *      maxMessage="Defina o parentesco usando no máximo {{ limit }} caracteres")
     */
    private $parentesco;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

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

    public function getParentesco(): ?string
    {
        return $this->parentesco;
    }

    public function setParentesco(string $parentesco): self
    {
        $this->parentesco = $parentesco;

        return $this;
    }
}

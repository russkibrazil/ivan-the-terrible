<?php

namespace App\Entity;

use App\Repository\SeioMaternoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     */
    private $dhInicio;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $dhFim;

    /**
     * @ORM\Column(type="string", length=1)
     * @Assert\NotBlank
     * @Assert\Choice(choices={"E","D"}, message="Utilize a inicial capital do lado (Esquerdo/Direito)")
     */
    private $lado;

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

    /**
     * Função para padronizar a comparação de tempo com as outras entidades. Permite deduzir que a mamada está concluída quando retornar um objeto DateTime
     *
     * @return \DateTimeInterface|null
     */
    public function getDh(): ?\DateTimeInterface
    {
        return $this->dhFim;
    }

    /**
     * Retorna o nome da classe para uso no Twig
     *
     * @return string
     */
    public function getClass(): string
    {
        return explode('\\', get_class())[2];
    }
}

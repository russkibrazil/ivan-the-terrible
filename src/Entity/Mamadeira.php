<?php

namespace App\Entity;

use App\Repository\MamadeiraRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     */
    private $dh;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank
     */
    private $alimento;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(message="A quantidade deve ser um valor maior que zero")
     */
    private $volume;

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

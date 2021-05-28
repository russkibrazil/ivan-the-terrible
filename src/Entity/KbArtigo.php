<?php

namespace App\Entity;

use App\Repository\KbArtigoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KbArtigoRepository::class)
 * @todo Verificar se é interessante colocar o resumo para apresentação na lista de resultados sugeridos
 */
class KbArtigo
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
    private $titulo;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $slug;

    /**
     * @ORM\Column(type="json")
     */
    private $tags = [];

    /**
     * @ORM\Column(type="text")
     */
    private $corpo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getCorpo(): ?string
    {
        return $this->corpo;
    }

    public function setCorpo(string $corpo): self
    {
        $this->corpo = $corpo;

        return $this;
    }
}

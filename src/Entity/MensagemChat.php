<?php

namespace App\Entity;

use App\Repository\MensagemChatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MensagemChatRepository::class)
 */
class MensagemChat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(referencedColumnName="email", nullable=false)
     */
    private $autor;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(referencedColumnName="email", nullable=false)
     */
    private $destinatario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dh;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $lido = [];

    /**
     * @ORM\Column(type="text")
     */
    private $mensagem;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAutor(): ?Usuario
    {
        return $this->autor;
    }

    public function setAutor(?Usuario $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    public function getDestinatario(): ?Usuario
    {
        return $this->destinatario;
    }

    public function setDestinatario(?Usuario $destinatario): self
    {
        $this->destinatario = $destinatario;

        return $this;
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

    public function getLido(): ?array
    {
        return $this->lido;
    }

    public function setLido(?array $lido): self
    {
        $this->lido = $lido;

        return $this;
    }

    public function getMensagem(): ?string
    {
        return $this->mensagem;
    }

    public function setMensagem(string $mensagem): self
    {
        $this->mensagem = $mensagem;

        return $this;
    }
}

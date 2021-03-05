<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 */
class Messages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $message = [];

    /**
     * @ORM\ManyToOne(targetEntity=Dialogs::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $dialog;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?array
    {
        return $this->message;
    }

    public function setMessage(array $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDialog(): ?Dialogs
    {
        return $this->dialog;
    }

    public function setDialog(?Dialogs $dialog): self
    {
        $this->dialog = $dialog;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /*public function __toString(): string
    {
        return (string) $this->getMessage();
    }*/
}

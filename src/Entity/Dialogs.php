<?php

namespace App\Entity;

use App\Repository\DialogsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DialogsRepository::class)
 */
class Dialogs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="array")
     */
    private $cryptoKey = [];

    /**
     * @ORM\OneToMany(targetEntity=DialogUsers::class, mappedBy="dialog", orphanRemoval=true)
     */
    private $dialogUsers;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="dialog", orphanRemoval=true)
     */
    private $messages;


    public function __construct()
    {
        $this->dialogUsers = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCryptoKey(): ?array
    {
        return $this->cryptoKey;
    }

    public function setCryptoKey(array $cryptoKey): self
    {
        $this->cryptoKey = $cryptoKey;

        return $this;
    }

    /**
     * @return Collection|DialogUsers[]
     */
    public function getDialogUsers(): Collection
    {
        return $this->dialogUsers;
    }

    public function addDialogUser(DialogUsers $dialogUser): self
    {
        if (!$this->dialogUsers->contains($dialogUser)) {
            $this->dialogUsers[] = $dialogUser;
            $dialogUser->setDialog($this);
        }

        return $this;
    }

    public function removeDialogUser(DialogUsers $dialogUser): self
    {
        if ($this->dialogUsers->removeElement($dialogUser)) {
            // set the owning side to null (unless already changed)
            if ($dialogUser->getDialog() === $this) {
                $dialogUser->setDialog(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Messages[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setDialog($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getDialog() === $this) {
                $message->setDialog(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

}

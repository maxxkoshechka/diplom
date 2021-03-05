<?php

namespace App\Entity;

use App\Repository\DialogUsersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DialogUsersRepository::class)
 */
class DialogUsers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Dialogs::class, inversedBy="dialogUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dialog;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="dialogUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
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
}

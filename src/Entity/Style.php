<?php

namespace App\Entity;

use App\Repository\StyleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StyleRepository::class)
 */
class Style
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameColor='black';

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $nameGlow;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="style", cascade={"persist", "remove"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameColor(): ?string
    {
        return $this->nameColor;
    }

    public function setNameColor(?string $nameColor): self
    {
        $this->nameColor = $nameColor;

        return $this;
    }

    public function getNameGlow(): ?string
    {
        return $this->nameGlow;
    }

    public function setNameGlow(?string $nameGlow): self
    {
        $this->nameGlow = $nameGlow;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getStyle() !== $this) {
            $user->setStyle($this);
        }

        $this->user = $user;

        return $this;
    }
}

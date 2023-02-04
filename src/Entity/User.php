<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *  fields = {"email"},
 *  message = "Adresse Email déjà utilisée."
 * )
 */
class User implements UserInterface
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="4", minMessage="Le mot de passse doit être composé par un minimum de 4 caractères.")
     * @Assert\EqualTo(propertyPath="confirm_password",message="Les 2 mots de passes doivent être identiques")
     */
    private $password;


    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


    /*
     * @Assert\EqualTo(propertyPath="password", message="Vous devez confirmer votre mot de passe")
     */
    public $confirm_password ;

    /**
     * @ORM\OneToMany(targetEntity=Shop::class, mappedBy="idUser")
     */
    private $shops;


    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $refered;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrRefs = 0;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user")
     */
    private $posts;
 

    /**
     * @ORM\Column(type="string", length=10,  nullable=true)
     */
    private $position;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->posts = new ArrayCollection();
    } 

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getRefered(): ?string
    {
        return $this->refered;
    }

    public function setRefered(string $refered): self
    {
        $this->refered = $refered;

        return $this;
    }


    public function getNbrRefs(): ?int
    {
        return $this->nbrRefs;
    }

    public function setNbrRefs(int $nbrRefs): self
    {
        $this->nbrRefs = $nbrRefs;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function eraseCredentials() {}
    public function getSalt() {}
    /*public function getRoles() {
        return ['ROLE_USER'] ; 
    }*/


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shop $shop): self
    {
        if (!$this->shops->contains($shop)) {
            $this->shops[] = $shop;
            $shop->setIdUser($this);
        }

        return $this;
    }

    public function removeShop(Shop $shop): self
    {
        if ($this->shops->removeElement($shop)) {
            // set the owning side to null (unless already changed)
            if ($shop->getIdUser() === $this) {
                $shop->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }


}

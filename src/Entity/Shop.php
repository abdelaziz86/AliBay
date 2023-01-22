<?php

namespace App\Entity;

use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 */
class Shop
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom ne peut pas être vide.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=500, nullable=true) 
     */
    private $image;

    /**
     * @ORM\Column(type="integer" , length=500, nullable=true) 
     */
    private $status = 1;

    /**
     * @ORM\Column(type="string", length=1500, nullable=true)
     */
    private $map;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $echeance;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idUser;

    /**
     * @ORM\ManyToOne(targetEntity=ShopCategory::class, inversedBy="shops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idShopCategory;

    /**
     * @ORM\OneToMany(targetEntity=CategoryProduit::class, mappedBy="IdShop")
     */
    private $categoryProduits;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="idShop")
     */
    private $produits;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     */
    private $visits = 0 ;

    public function __construct()
    {
        $this->categoryProduits = new ArrayCollection();
        $this->produits = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?int $image): self
    {
        $this->status = $image;

        return $this;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(?string $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getEcheance(): ?\DateTimeInterface
    {
        return $this->echeance;
    }

    public function setEcheance(?\DateTimeInterface $echeance): self
    {
        $this->echeance = $echeance;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdShopCategory(): ?ShopCategory
    {
        return $this->idShopCategory;
    }

    public function setIdShopCategory(?ShopCategory $idShopCategory): self
    {
        $this->idShopCategory = $idShopCategory;

        return $this;
    }

    /**
     * @return Collection<int, CategoryProduit>
     */
    public function getCategoryProduits(): Collection
    {
        return $this->categoryProduits;
    }

    public function addCategoryProduit(CategoryProduit $categoryProduit): self
    {
        if (!$this->categoryProduits->contains($categoryProduit)) {
            $this->categoryProduits[] = $categoryProduit;
            $categoryProduit->setIdShop($this);
        }

        return $this;
    }

    public function removeCategoryProduit(CategoryProduit $categoryProduit): self
    {
        if ($this->categoryProduits->removeElement($categoryProduit)) {
            // set the owning side to null (unless already changed)
            if ($categoryProduit->getIdShop() === $this) {
                $categoryProduit->setIdShop(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setIdShop($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getIdShop() === $this) {
                $produit->setIdShop(null);
            }
        }

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(?int $visits): self
    {
        $this->visits = $visits;

        return $this;
    }
}

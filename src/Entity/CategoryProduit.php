<?php

namespace App\Entity;

use App\Repository\CategoryProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryProduitRepository::class)
 */
class CategoryProduit
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
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="categoryProduits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $IdShop;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="idCategoryProduit")
     */
    private $produits;

    public function __construct()
    {
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

    public function getIdShop(): ?Shop
    {
        return $this->IdShop;
    }

    public function setIdShop(?Shop $IdShop): self
    {
        $this->IdShop = $IdShop;

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
            $produit->setIdCategoryProduit($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getIdCategoryProduit() === $this) {
                $produit->setIdCategoryProduit(null);
            }
        }

        return $this;
    }
}

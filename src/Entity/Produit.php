<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
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
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $featured;

    /**
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idShop;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryProduit::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idCategoryProduit;

    /**
     * @ORM\Column(type="integer", nullable=true , options={"default" : 0})
     */
    private $visits = 0 ;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $featuredHome;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $disponible;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getFeatured(): ?int
    {
        return $this->featured;
    }

    public function setFeatured(int $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getIdShop(): ?Shop
    {
        return $this->idShop;
    }

    public function setIdShop(?Shop $idShop): self
    {
        $this->idShop = $idShop;

        return $this;
    }

    public function getIdCategoryProduit(): ?CategoryProduit
    {
        return $this->idCategoryProduit;
    }

    public function setIdCategoryProduit(?CategoryProduit $idCategoryProduit): self
    {
        $this->idCategoryProduit = $idCategoryProduit;

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

    public function getFeaturedHome(): ?int
    {
        return $this->featuredHome;
    }

    public function setFeaturedHome(?int $featuredHome): self
    {
        $this->featuredHome = $featuredHome;

        return $this;
    }

    public function getDisponible(): ?int
    {
        return $this->disponible;
    }

    public function setDisponible(?int $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }
}

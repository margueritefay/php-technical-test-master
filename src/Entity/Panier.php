<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 *
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="paniers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PanierProduit::class, mappedBy="panier", orphanRemoval=true)
     */
    private $produits;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $valorisation;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $validated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codePromo;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, PanierProduit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(PanierProduit $panierProduit): self
    {
        if (!$this->produits->contains($panierProduit)) {
            $this->produits[] = $panierProduit;
            $panierProduit->setPanier($this);
        }

        return $this;
    }

    public function removeProduit(PanierProduit $panierProduit): self
    {
        if ($this->produits->removeElement($panierProduit)) {
            // set the owning side to null (unless already changed)
            if ($panierProduit->getPanier() === $this) {
                $panierProduit->setPanier(null);
            }
        }

        return $this;
    }

    public function getValorisation(): ?int
    {
        return $this->valorisation;
    }

    public function setValorisation(int $valorisation): self
    {
        $this->valorisation = $valorisation;

        return $this;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getCodePromo(): ?string
    {
        return $this->codePromo;
    }

    public function setCodePromo(?string $codePromo): self
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}

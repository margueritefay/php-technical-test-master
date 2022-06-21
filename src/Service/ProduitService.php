<?php
namespace App\Service;

use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProduitService
{

    private $em;

    public function __construct(
        EntityManagerInterface $em
    )   {
        $this->em = $em;
    }
    public function stockProduit(Panier $panier)
    {
        /** @var PanierProduit $panierProduit */
        foreach ($panier->getProduits() as $panierProduit) {
           $produit = $panierProduit->getProduit();
           $produit->setStock($produit->getStock() - $panierProduit->getQuantity());
            $this->em->persist($produit);
        }
        $this->em->flush();
    }

}

<?php
namespace App\Service;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

class PromoService
{

    private $em;

    public function __construct(
        EntityManagerInterface $em
    )   {
        $this->em = $em;
    }
    public function newPanier(Produit $produit)
    {
        $panier = new Panier();
        $panier->addProduit($produit);

        return $panier;
    }

    public function valorisationPanier(Panier $panier){
        //faire une méthode pour récupérer tous les produits d'un panier

        //prendre en compte les promos

        return;
    }
}

<?php
namespace App\Service;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProduitService
{

    private $produitRepository;
    private $em;

    public function __construct(
        ProduitRepository $produitRepository,
        EntityManagerInterface $em
    )   {
        $this->produitRepository = $produitRepository;
        $this->em = $em;
    }
    public function stockProduit(Produit $produit)
    {
        $panier = new Panier();
        $panier->addProduit($produit);

        return $panier;
    }

}

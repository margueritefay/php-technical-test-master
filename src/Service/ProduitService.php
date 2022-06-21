<?php
namespace App\Service;

use App\Entity\Panier;
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
    public function stockProduit(Produit $produit)
    {
        //on considert l'achat d'un produit par un produit
        $produit->setStock($produit->getStock() - 1);
        $this->em->persist($produit);
        $this->em->flush();
    }

}

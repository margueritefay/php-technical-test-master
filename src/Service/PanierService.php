<?php
namespace App\Service;

use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

class PanierService
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

    public function checkAvailableCart (Panier $panier) {
        if($panier->isValidated()) {
            throw new \Exception("Ce panier a déjà été validé");
        }
    }

    public function valorisationPanier(Panier $panier){
        $valorisation = 0;
        /** @var PanierProduit $panierProduit */
        foreach ($panier->getProduits() as $panierProduit) {
            $valorisation += $panierProduit->getQuantity() * $panierProduit->getProduit()->getPrix();
        }
        $panier->setValorisation($valorisation);
        $this->em->persist($panier);
        $this->em->flush();
    }
}

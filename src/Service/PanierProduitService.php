<?php
namespace App\Service;

use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Produit;
use App\Repository\PanierProduitRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

class PanierProduitService
{

    private $em;
    private $panierProduitRepository;
    private PanierService $panierService;

    public function __construct(
        EntityManagerInterface $em,
        PanierProduitRepository $panierProduitRepository,
        PanierService $panierService
    )   {
        $this->em = $em;
        $this->panierProduitRepository = $panierProduitRepository;
        $this->panierService = $panierService;
    }

    public function checkProduit(PanierProduit $panierProduit) {
        //on verifie qu'il y a un produit dans le panier
        if($panierProduit->getProduit() == null || $panierProduit->getQuantity() < 1) {
            throw new \Exception("Il faut ajouter un produit au panier");
        }

        //on verifie le stock
        if($panierProduit->getQuantity() > $panierProduit->getProduit()->getStock()) {
            throw new \Exception("Pas assez de stock pour ce produit");
        }
    }

    public function addPanierProduit(PanierProduit $panierProduit)
    {
        //on ajoute le panier
        if($panierProduit->getPanier() == null) {
            $panierProduit->setPanier(new Panier());
        } else {
            //on verifie que le panier n'est pas validé
            $this->panierService->checkAvailableCart($panierProduit->getPanier());

            //on verifie que le produit n'existe pas déjà
            $panierProduitDB = $this->panierProduitRepository->findOneBy(["panier"=>$panierProduit->getPanier(), "prduit"=>$panierProduit->getProduit()]);
            if($panierProduitDB) {
                $panierProduitDB->setQuantity($panierProduit->getQuantity());
                $panierProduit = $panierProduitDB;
            }
        }

        $this->em->persist($panierProduit);
        $this->em->flush();
        return $panierProduit;
    }
}

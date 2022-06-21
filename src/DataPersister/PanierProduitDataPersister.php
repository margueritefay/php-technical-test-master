<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Service\PanierProduitService;
use App\Service\PanierService;
use App\Service\ProduitService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PanierProduitDataPersister implements DataPersisterInterface
{

    private $security;
    private $em;
    private $panierService;
    private $panierProduitService;
    private ProduitService $produitService;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        PanierService $panierService,
        PanierProduitService $panierProduitService,
        ProduitService $produitService
    ){
        $this->security = $security;
        $this->em = $em;
        $this->panierService = $panierService;
        $this->panierProduitService = $panierProduitService;
        $this->produitService = $produitService;
    }

    /**
     * @param PanierProduit $data
     */
    public function supports($data): bool
    {
        return $data instanceof PanierProduit;
    }

    /**
     * @param PanierProduit $panierProduit
     */
    public function persist($panierProduit)
    {
        //on verifie le produit
        $this->panierProduitService->checkProduit($panierProduit);

        //on ajoute le panier et on verifie les quantités du produit
        $this->panierProduitService->addPanierProduit($panierProduit);

        //on valorise le panier
        $this->panierService->valorisationPanier($panierProduit->getPanier());
    }

    /**
     * @param PanierProduit $data
     */
    public function remove($data)
    {
        $this->em->remove($data);
        $this->em->flush();
    }
}

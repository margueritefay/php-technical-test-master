<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Service\ProduitService;
use App\Service\PromoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PanierDataPersister implements DataPersisterInterface
{

    private $security;
    private $em;
    private PanierRepository $panierRepository;
    private ProduitService $produitService;
    private PromoService $promoService;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        PanierRepository $panierRepository,
        ProduitService $produitService,
        PromoService $promoService
    ){
        $this->security = $security;
        $this->em = $em;
        $this->panierRepository = $panierRepository;
        $this->produitService = $produitService;
        $this->promoService = $promoService;
    }

    /**
     * @param Panier $data
     */
    public function supports($data): bool
    {
        return $data instanceof Panier;
    }

    /**
     * @param Panier $panier
     */
    public function persist($panier)
    {
        //on valide le panier
        if($panier->isValidated()) {
            $oldPanier = $this->panierRepository->find($panier->getId());
            //le panier vient d'être validé
            if($oldPanier && !$oldPanier->isValidated()) {

                //on revoit les stocks
                $this->produitService->stockProduit($panier);

                //on applique les promos
                $this->promoService->applyPromotion($panier);
            }
        }
        $this->em->persist($panier);
        $this->em->flush();
    }

    /**
     * @param Panier $data
     */
    public function remove($data)
    {
        $this->em->remove($data);
        $this->em->flush();
    }
}

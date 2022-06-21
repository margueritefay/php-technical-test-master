<?php
namespace App\Service;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Repository\PromoRepository;
use Doctrine\ORM\EntityManagerInterface;

class PromoService
{

    private $em;
    private PromoRepository $promoRepository;
    private ProduitRepository $produitRepository;
    private PanierService $panierService;

    public function __construct(
        EntityManagerInterface $em,
        PromoRepository $promoRepository,
        ProduitRepository $produitRepository,
        PanierService $panierService
    )   {
        $this->em = $em;
        $this->promoRepository = $promoRepository;
        $this->produitRepository = $produitRepository;
        $this->panierService = $panierService;
    }


    public function applyPromotion(Panier $panier){
        if($panier->getCodePromo() != null) {
            $promotion = $this->promoRepository->findOneBy(["code" => $panier->getCodePromo()]);
            if($promotion) {
                switch ($promotion->getCode()) {
                    case "promo1":
                        $panier->setValorisation($this->promoOne($panier));
                        break;
                    case "promo2" :
                        $panier->setValorisation($this->promoTwo($panier));
                        break;
                    case "promo3" :
                        $panier->setValorisation($this->promoThree($panier));
                        break;
                }
            }
        }
        $this->em->persist($panier);
        $this->em->flush();
    }

    private function promoOne(Panier $panier) {
       return $panier->getValorisation()-(($panier->getValorisation()*20) /100);
    }

    private function promoTwo(Panier $panier) {
        $type= "a definir";
        $produitsType =  $this->produitRepository->findBy(['panier' => $panier, 'type' => $type],['prix' => 'ASC'] );
        if(count($panier->getProduits()) > 2 && $produitsType > 2){
            $produitReduit = $this->produitRepository->findOneBy(['panier' => $panier, 'type' => $type],['prix' => 'ASC'] );
            return $panier->getValorisation()-$produitReduit->getPrix();
        }
        else{
            throw new \Exception("Promo invalide sur vos produits");
        }
    }

    private function promoThree(Panier $panier) {
        if(count($panier->getProduits()) > 2  && $panier->getValorisation() > 40){
            $produits =  $this->produitRepository->findBy(['panier' => $panier]);
            $reductionOne = $produits[0]->getPrix()-(($produits[0]->getPrix()* 60 ) / 100);
            $reductionTwo = $produits[1]->getPrix()-(($produits[1]->getPrix()* 40 ) / 100);
            $reductionThree = $produits[2]->getPrix()-(($produits[2]->getPrix()* 20 ) / 100);
            return $panier->getValorisation()- $reductionOne - $reductionTwo - $reductionThree;
        }
        else{
            throw new \Exception("Promo invalide sur vos produits");
        }
    }
}

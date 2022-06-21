<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Service\PanierService;
use App\Service\ProduitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="create_panier")
     */
    public function createPanier(Produit $produit, PanierService $panierSerice, ProduitService $produitService): Response
    {
        //creation d'un panier et ajout du produit dans le panier
        $panier = $panierSerice->newPanier($produit);

        // valorisation du panier (ou mise à jour)
        $panierSerice->valorisationPanier($panier);

        //validation du panier

        //gestion des stocks de produit
        $produitService->stockProduit($produit);


        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }
}

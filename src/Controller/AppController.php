<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppController extends AbstractController
{
    // Route pour aller à la page d'accueil...
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {

        /*
Exo : 
1- Sélectionner tout les produits enregistrés en BDD (repository)
2- Transmettre au template les produits sélectionnés (render())
3- Réaliser le traitement permettant d'afficher les produits dans le template 'app/index.html'.
4- Créer une nouvelle méthode appProductDetails avec la route 'app/product/detail/{id}'  /app_product_details, nouveau template 'app/product.details.html.twig'
5- Sélectionner en BDD le produit
6- Afficher les informations du produit (titre, référence, image etc... )
        */

        $products = $productRepository->getMaxProducts();
        dump($products);

        return $this->render('app/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/products/details/{id}', name: 'app_products_details')]
    public function appProductDetails($id, ProductRepository $productRepository): Response
    {
        $products = $productRepository->find($id);

        return $this->render('app/products.details.html.twig', [
            'product' => $products
        ]);
    }

    // Route pour la page about
    #[Route('/about', name: 'app_about')]
    public function appAbout(): Response
    {
        return $this->render('app/about.html.twig');
    }

    // Route pour la page products
    #[Route('/products', name: 'app_products')]
    public function appProducts(): Response
    {
        return $this->render('app/products.html.twig');
    }

    // Route pour la page why
    #[Route('/why', name: 'app_why')]
    public function appWhy(): Response
    {
        return $this->render('app/why.html.twig');
    }

    // Route pour la page testimonial
    #[Route('/testimonial', name: 'app_testimonial')]
    public function appTestimonial(): Response
    {
        return $this->render('app/testimonial.html.twig');
    }

    // Route pour la page my_account
    #[Route('/my_account', name: 'app_my_account')]
    public function appMyAccount(): Response
    {
        return $this->render('app/my_account.html.twig');
    }
}

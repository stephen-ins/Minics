<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    // Route pour aller à la page d'accueil...
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', []);
    }

    #[Route('/products', name: 'app_products')]
    public function appProducts(): Response
    {
        return $this->render('app/products.html.twig', []);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppController extends AbstractController
{
    // Route pour aller à la page d'accueil...
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
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
}

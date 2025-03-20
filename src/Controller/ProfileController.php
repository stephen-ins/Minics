<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/my_account', name: 'app_my_account')]
    public function myAccount(): Response
    {
        $user = $this->getUser();
        return $this->render('app/my_account.html.twig', []);
    }
}

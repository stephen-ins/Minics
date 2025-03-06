<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        // dump($request);

        $user = new User();
        // dump($user);

        $form = $this->createForm(RegistrationFormType::class, $user);
        // dump($request);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('password')->getData();
            $passwordHash = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($passwordHash);

            // prepare("insert into user values($user->getFirstName())")
            $entityManager->persist($user);
            $entityManager->flush();

            dump($passwordHash);
            dump($user);

            return $this->redirectToRoute('app_home');
        }
        return $this->render('registration/register.html.twig', ['registrationForm' => $form]);
    }
}

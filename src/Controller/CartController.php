<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function cart(SessionInterface $session, ProductRepository $repoProduct): Response
    {

        // ON récupère le panier dans la session
        $cart = $session->get('cart', []);
        // dump($cart);

        // On initialise un tableau vide pour stocker les produits
        // et une variable pour le total
        $dataCart = [];
        $total = 0;

        // On boucle la session pour récupérer les produits
        // et la quantité de chaque produit
        foreach ($cart as $id => $quantity) {
            // dump($id);
            // dump($quantity);

            // ON récupère le produit dans la BDD
            $product = $repoProduct->find($id);
            // dump($product);

            // ON vérifie si le produit existe
            // On ajoute dans le tableau Array les données du produit
            $dataCart[] = [
                'product' => $product, // On envoi l'objet Entity Product dans le tableau Array
                'quantity' => $quantity,
            ];

            // ON calcule le total de la commande
            $total += $product->getPrice() * $quantity;
        }

        dump($dataCart);
        dump($total);

        return $this->render('cart/index.html.twig', [
            'dataCart' => $dataCart,
            'total' => $total,
        ]);
    }





    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function cartAdd(Request $request, Product $product, SessionInterface $session)
    {
        // dump($request);
        // dump($product);

        // Création du panier dans la session
        $cart = $session->get('cart', []);

        // ON stocke le produit dans le panier dans une variable
        $id = $product->getId();

        // ON stock la quantité saisie dans le formulaire dans une variable
        $quantity = $request->request->get('quantity');

        // dump($id);
        // dump($quantity);

        //          $cart[1]
        if (isset($cart[$id])) {

            // dump('if produit existe dans la panier');

            // 
            $cart[$id] = $cart[$id] + $quantity;
        } else {

            // dump('else produit inexistant dans la panier');

            // 
            $cart[$id] = $quantity;
        }

        // ON sauvegarde le panier dans la session
        $session->set('cart', $cart);
        // dump($cart);

        /*

        $idProduit => quantity
        10                  3
        4                   9

        */

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function cartRemove(Product $product, SessionInterface $session)
    {
        $cart = $session->get('cart', []);
        $id = $product->getId();

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }
}

<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Product;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        $this->addFlash('success', 'Le produit a bien été supprimé du panier');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/', name: 'app_cart_delete')]
    public function cartDeleteAll(SessionInterface $session)
    {
        $session->remove('cart', []);

        $this->addFlash('success', 'Le panier a bien été vidé');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/update/{id}', name: 'app_cart_update')]
    public function cartUpdate(Request $request, Product $product, SessionInterface $session)
    {
        $cart = $session->get('cart', []);
        $id = $product->getId();

        $quantity = (int)$request->request->get('quantity');

        if (isset($cart[$id]) && $quantity > 0) {
            $cart[$id] = $quantity;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/payment', name: 'app_cart_payment')]
    public function cartPayment(SessionInterface $session, ProductRepository $repoProduct, EntityManagerInterface $entityManager)
    {
        $cart = $session->get('cart', []);
        $total = 0;
        dump($cart);

        foreach ($cart as $id => $quantity) {
            $product = $repoProduct->find($id);
            $stockDb = $product->getStock();
            // dump($stockDb);

            if ($stockDb < $quantity) {
                if ($stockDb > 0) {
                    // On entre dans la condition si le stock est insuffisant par rapport à la quantité demandée
                    dump('Stock insuffisant pour le produit : ' . $product->getTitle());
                    dump('Stock restant : ' . $stockDb);
                    dump('Quantité commandée : ' . $quantity);

                    $this->addFlash('warning', 'La quantité du produit <strong>' . $product->getTitle() . '</strong> a été modifiée car le stock est insuffisant. Stock actuel : ' . $stockDb);

                    $cart[$id] = $stockDb;
                } else {
                    // Sinon le stock est à 0, alors on supprime le produit du panier
                    dump('Article : ' . $product->getTitle()) . " est en rupture de stock";
                    dump('Stock restant : ' . $stockDb);
                    dump('Quantité commandée : ' . $quantity);

                    $this->addFlash('danger', 'Le produit <strong>' . $product->getTitle() . '</strong> a été retiré du panier car il est en rupture de stock');

                    // On supprime l'id et la quantité du produit dans la session
                    unset($cart[$id]);
                }

                $error = true;

                // On met à jour le panier dans la session
                $session->set('cart', $cart);
            }

            $total += $product->getPrice() * $quantity;
        }

        // Requete d'insertion dans la table orders
        if (!isset($error)) {
            $order = new Orders();
            $order->setUser($this->getUser());
            // On génère le numéro de commande
            // MINICS-01012023-123456789
            $orderNumber = "MINICS-" . date('dmY') . '-' . uniqid();
            $order->setOrderNumber($orderNumber);
            $order->setRising($total);
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setState('En cours de traitement');

            $entityManager->persist($order);
            $entityManager->flush();


            // Insertion dans la table order_details

            foreach ($cart as $id => $quantity) {
                $orderDetails = new OrderDetails();

                $product = $repoProduct->find($id);
                $orderDetails->setOrders($order);
                $orderDetails->setProduct($product);
                $orderDetails->setQuantity($quantity);
                $orderDetails->setPrice($product->getPrice());

                // ON déprécie les stocks
                $product->setStock($product->getStock() - $quantity);

                $entityManager->persist($product);
                $entityManager->persist($orderDetails);
                $entityManager->flush();
            }


            $this->addFlash('success', 'Votre commande n°= <strong>' . $orderNumber . '</strong> a bien été enregistrée. Merci de votre fidélité !');

            $session->remove('cart');
        }

        return $this->redirectToRoute('app_cart');
    }
}

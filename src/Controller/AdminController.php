<?php

namespace App\Controller;

use PDO;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductFormType;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/admin/products', name: 'app_admin_products')]
    #[Route('admin/products/update{id}', name: 'app_admin_products_update')]
    public function adminProducts(?Product $product, Request $request, EntityManagerInterface $entity_manager, SluggerInterface $slugger, ProductRepository $repoProduct): Response
    {

        // _?Product $product : le ? veut dire que par défaut $product à une valeur null

        if (!$product) {
            $product = new Product;
        }
        // dump($product);


        $product = new Product;
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();

            // dump($pictureFile);

            if ($pictureFile) {

                // retourne le nom du fichier d'origine sans extension
                $originalFilefName = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // dump($originalFilefName);

                // slug() sécurise le nom du fichier (supression espace etc... )
                $safeFileName = $slugger->slug($originalFilefName);
                // dump($safeFileName);

                // On renomme l'image
                //                                  p4-67d02b10eb963.png
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                // dump($newFileName);
                // dump($this->getParameter('image_directory'));
                $currentPath = $this->getParameter('image_directory');

                try {

                    // le try va tenter de copier l'image 
                    $pictureFile->move($currentPath, $newFileName);
                } catch (FileException $e) {
                    // dump($e);
                }

                $product->setPicture($newFileName);
                dump($product);
            }

            $product->setCreatedAt(new \DateTimeImmutable());
            $entity_manager->persist($product);
            $entity_manager->flush();

            $this->addFlash('success', "L'article a été enregistré avec succès.");

            return $this->redirectToRoute('app_admin_products');
        }

        // repoProduct = objet issu de la class ProductRepository
        $repoProduct = $entity_manager->getRepository(Product::class);
        $dbProduct = $repoProduct->findAll();
        // dump($dbProduct);

        return $this->render('admin/products.html.twig', [
            'productForm' => $form,
            'dbProduct' => $dbProduct,
            'pictureFile' => $product->getPicture()
        ]);

        return $this->render('admin/products.html.twig', [
            'productForm' => $form
        ]);
    }


    // #[Route('admin/products/update{id}', name: 'app_admin_products_update')]
    // public function adminProductsUpdate($id, $product, Request $request, EntityManagerInterface $entityManager, ProductRepository $repoProducts): Response
    // {
    //     $product = $repoProducts->find($id);
    //     dump($id);
    //     // dump($category);

    //     $form = $this->createForm(ProductFormType::class, $product);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($product);
    //         $entityManager->flush();

    //         $productTitle = $product->getTitle();
    //         // dump($productTitle);

    //         $this->addFlash('success', "Le produit  <strong class='text-white'> {$productTitle} </strong> a été mise à jour.");
    //         // return $this->redirectToRoute('app_admin_product');
    //     }

    //     $dbProduct = $repoProducts->findAll();

    //     return $this->render('admin/products.html.twig', [
    //         'productForm' => $form,
    //         'dbProduct' => $dbProduct
    //     ]);
    // }




    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function adminOrders(): Response
    {
        return $this->render('admin/order.html.twig', []);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function adminUsers(): Response
    {
        return $this->render('admin/users.html.twig', []);
    }

    #[Route('/admin/category ', name: 'app_admin_category')]
    public function adminCategory(Request $request, EntityManagerInterface $entityManager, CategoryRepository $repoCategory): Response
    {
        $category = new Category;

        $form = $this->createForm(CategoryFormType::class, $category);

        // $category->setTitle($_POST['title'])
        $form->handleRequest($request);

        // if(issert($_POST['submit'])) && $SERVER['REQUEST_METHOD'] == 'POST') 
        if ($form->isSubmitted() && $form->isValid()) {

            $category->setCreatedAt(new \DateTimeImmutable());

            // $connect_db->prepare("INSERT INTO category VALUES (:title)")
            // $connect_db->bindValue(':title', $category->getTitle(), PDO::PARAM_STR);
            $entityManager->persist($category);

            // $stmt->execute()
            $entityManager->flush();

            // dump($request);
            // dump($category);

            // Message utilisateur stocké dans la session
            // $_SESSION['message_validate'] = "La catégorie a été enregistrée.";
            // $stmt->bindvalue(':title...
            $this->addFlash('success', "La catégorie a été enregistrée.");
            // $this->addFlash('danger', "Echec du rajout de la catégorie.");

            return $this->redirectToRoute('app_admin_category');
        }

        // $data = $connect_db->query('SELECT * FROM category');
        // $dbCategory = $data->fechAll(PDO::FETCH_ASSOC);

        // Une classe Repository contient des méthodes permettant uniquement d'executer des requêtes de sélections (SELECT) en BDD (find($id)), findAll(), findBy(), findOneBy()

        $dbCategory = $repoCategory->findAll();
        // dump($dbCategory);


        return $this->render('admin/category.html.twig', [
            'categoryForm' => $form,
            'dbCategory' => $dbCategory
        ]);
    }

    // UPDATE                       1
    #[Route('admin/category/update{id}', name: 'app_admin_category_update')]
    public function adminCategoryUpdate($id, Category $category, Request $request, EntityManagerInterface $entityManager, CategoryRepository $repoCategory): Response
    {

        // SELECT * FROM category WHERE id = $id; // 1
        // + fetch(PDO::FETCH_ASSOC);
        $category = $repoCategory->find($id);

        // dump($id);
        // dump($category);

        $form = $this->createForm(CategoryFormType::class, $category);

        // $category->setTitle($_POST['title'])
        $form->handleRequest($request);

        // UPDATE category SET title = $category->getTitle(), description = $category->getDescription() WHERE id = $id;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $categoryTitle = $category->getTitle();
            // dump($categoryTitle);

            $this->addFlash('success', "La catégorie  <strong class='text-white'> {$categoryTitle} </strong> a été mise à jour.");
            return $this->redirectToRoute('app_admin_category');
        }

        $dbCategory = $repoCategory->findAll();

        return $this->render('admin/category.html.twig', [
            'categoryForm' => $form,
            'dbCategory' => $dbCategory
        ]);
    }

    #[Route('admin/category/remove{id}', name: 'app_admin_category_remove')]
    public function adminCategoryRemove($id, EntityManagerInterface $entityManager, CategoryRepository $repoCategory)
    {
        $category = $repoCategory->find($id);
        dump($category);

        // DELETE FROM category WHERE id = $id;
        // $connect_db->prepare("DELETE FROM category WHERE id = :id");
        // $connect_db->bindValue(':id', $id, PDO::PARAM_INT);
        // $connect_db->execute();
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', "La catégorie a bien été supprimée.");
        return $this->redirectToRoute('app_admin_category');
    }
}

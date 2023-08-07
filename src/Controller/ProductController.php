<?php

namespace App\Controller;

Use Symfony\Component\Routing\Annotation\Route;
Use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
Use Doctrine\ORM\EntityManagerInterface;
Use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductType;
use App\Tax\CalculTva;

class ProductController extends AbstractController{

    #[Route('/product', name: 'app_product')]
    public function index(EntityManagerInterface $entityManager){

        $products = $entityManager->getRepository(Product::class)->findBy(['valid' => true]);

        return $this->render('products/index.html.twig', [
            'products' => $products
        ]);

    }

    #[Route('/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $manager, CalculTva $calculTva){

        
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setValid(true);

            $product->setPrice($this->$calculTva->CalculTTC($product->getPrice()));

            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'Produit créer avec succès ! produit : '.$product->getName());

            return $this->redirectToRoute('app_product');
        }

        return $this->render('products/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show($id, EntityManagerInterface $entityManager){

        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        if (is_null($product)) {
            return $this->redirectToRoute('app_product');
        }

        return $this->render('products/show.html.twig', [
            'product' => $product
        ]);

    }

}
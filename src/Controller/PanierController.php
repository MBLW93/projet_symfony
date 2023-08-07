<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
Use Doctrine\ORM\EntityManagerInterface;
Use App\Entity\Panier;
Use App\Entity\User;
Use App\Repository\PanierRepository;
Use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierRepository $panierRepository, SessionInterface $session, UserRepository $userRepository, Security $security): Response
    {
        

        if ($security !== null && $security->isGranted('IS_AUTHENTICATED_FULLY')){
            $user = $security->getUser();
            $id = $user->getId();
        }

        $panier = $panierRepository->findOneBy(['user' => $id]);
        $products = $panier->getProduct();

        $totalPrice = 0;
        $array_cart = [];
        foreach ($products as $product) {
            $totalPrice += $product->getPrice();
            //array_push($array_cart, $product->getId());
        }

        $session->set('cart', $panier->getId());

        $session->set('cart_item_count', count($products));

       
     
        return $this->render('panier/index.html.twig', [
            'products' => $products,
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * @Route("/cart/count", name="cart_count")
     */
    public function getCartItemCount(SessionInterface $session): Response
    {
        // Récupérez le nombre d'articles du panier depuis votre système de stockage (dans ce cas, la session)
        $cartItemCount = $session->get('cart_item_count', 0);

        // Vous pouvez également utiliser votre service de gestion de panier pour obtenir le nombre d'articles du panier.

        return $this->json(['count' => $cartItemCount]);
    }
}

<?php

// namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;

// class StripeController extends AbstractController
// {
//     #[Route('/stripe', name: 'app_stripe')]
//     public function index(): Response
//     {
//         return $this->render('stripe/index.html.twig', [
//             'controller_name' => 'StripeController',
//         ]);
//     }
// }

// src/Controller/StripeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
Use App\Repository\PanierRepository;
Use Doctrine\ORM\EntityManagerInterface;

class StripeController extends AbstractController
{

    
    #[Route('/paiement/{amount}', name: 'payment')]
    public function showPaymentForm(float $amount)
    {
        $stripePublicKey = $this->getParameter('stripe_public_key');
        $total = intval($amount*100);
        //dd($session->get('cart', 0));

        return $this->render('payment.html.twig', [
            'stripe_public_key' => $stripePublicKey,
            'amount' => $total,
        ]);
    }

    #[Route("/create_payment/{amount}", name: "create_payment", methods: "POST")]
    public function createPayment($amount, Request $request, Security $security, SessionInterface $session, PanierRepository $panierRepository, EntityManagerInterface $entityManager)
    {
        $secretKey = $this->getParameter('stripe_secret_key');
        Stripe::setApiKey($secretKey);

        $paymentToken = $request->request->get('payment_token');

        try {
           
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'eur',
                'payment_method' => $paymentToken,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            // if ($security !== null && $security->isGranted('IS_AUTHENTICATED_FULLY')){
                $user = $security->getUser();
                $id = $user->getId();
            // }
    
            $panier = $panierRepository->findOneBy(['id' => $session->get('cart', 0)]);
            $products = $panier->getProduct();

            foreach ($products as $product){
                $panier->removeProduct($product);
            }

            $entityManager->persist($panier);
            $entityManager->flush();

            

            return new Response('Paiement créé avec succès !');
        } catch (\Stripe\Exception\CardException $e) {
            return new Response('Erreur lors du paiement : ' . $e->getMessage());
        } catch (\Stripe\Exception\StripeException $e) {
            return new Response('Erreur Stripe : ' . $e->getMessage());
        }
    }
}


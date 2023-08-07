<?php

namespace App\Controller;

Use Psr\Log\LoggerInterface;
Use Symfony\Component\Routing\Annotation\Route;
Use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
Use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route(name: 'app_')]


class MainController extends AbstractController{


    #[Route('/', name: 'main')]
    public function home(Security $security){

        if ($security !== null && $security->isGranted('IS_AUTHENTICATED_FULLY')){
            $user = $security->getUser();
            $username = $user->getUsername();
        }else{
            return $this->render('main.html.twig', [
                'userExist' => 0
            ]);
        }

        return $this->render('main.html.twig', [
            'username' => $username,
            'userExist' => 1
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(){
        return $this->render('contact.html.twig');
    }

    

}

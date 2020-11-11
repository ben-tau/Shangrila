<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    
    class HomeController extends AbstractController
    {
        /**
         * Page d'accueil
         * @Route("/",name="homepage")
         * @return Response
         */
        public function home()
        {
            return $this->render('home.html.twig');
        }
    }
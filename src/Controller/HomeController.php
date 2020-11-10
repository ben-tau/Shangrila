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

        /**
         * Affiche la page de connexion
         * @Route("/login",name="login")
         * @return Response
         */
        public function login()
        {
            return $this->render('account/login.html.twig');
        }

        /**
         * Affiche la page d'inscription
         * @Route("/register",name="register")
         * @return Response
         */
        public function register()
        {
            return $this->render('account/register.html.twig');
        }

        /**
         * Affiche la page des commandes possibles
         * @Route("/order",name="order")
         * @return Response
         */
        public function order()
        {
            return $this->render('order/index.html.twig');
        }
    }
<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    
    class HomeController extends AbstractController
    {
        /**
         * Page d'accueil
         * @Route("/",name="homepage")
         * @return Response
         */
        public function home(BookingRepository $repo,Request $request,EntityManagerInterface $entityManager,FrToDatetimeTransformer $transformer)
        {
            $repo->findAll();

            $booking = new Booking();

            if(isset($_POST) && isset($_POST['bookingDate']) && isset($_POST['bookingTime']) && isset($_POST['personsNumber']))
            {
                $time = substr($_POST['bookingTime'],0,-3);
                $bookingDate = $_POST['bookingDate'].' '.$_POST['bookingTime'];
                $formatedBookingDate = $transformer->reverseTransform($bookingDate);
                $personsNumber = (int) $_POST['personsNumber'];

                $booking->setUser($this->getUser())
                        ->setDate($formatedBookingDate)
                        ->setPersonsNumber($personsNumber)
                ;

                $entityManager->persist($booking);
                $entityManager->flush();

                $date = date_create($bookingDate);
                $date = date_format($date, 'd/m/Y');

                if($personsNumber == 1)
                {
                    $persons = "personne";
                }
                else
                {
                    $persons = "personnes";
                }

                $this->addFlash("success","Votre réservation pour le $date à $time"."h"." pour $personsNumber $persons a bien été prise en compte.");
            }

            return $this->render('home.html.twig',[

            ]);
        }
    }
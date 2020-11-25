<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\DataTransformer\FrToDatetimeTransformer;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings")
     */
    public function index(BookingRepository $bookingRepo): Response
    {
        $bookings = $bookingRepo->findAll();

        return $this->render('admin/bookings/index.html.twig', [
            'bookings'=>$bookings
        ]);
    }

    /**
     * Edition d'une réservation par l'admin
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function edit(Booking $booking,Request $request,EntityManagerInterface $entityManager,FrToDateTimeTransformer $transformer)
    {
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

            $this->addFlash("success","La réservation n°{$booking->getId()} pour le $date à $time"."h"." pour $personsNumber $persons a bien été modifiée.");
        }

        return $this->render('admin/bookings/edit.html.twig', [
            'booking' => $booking
        ]);
    }

    /**
     * Suppression d'une réservation par l'admin
     * @Route("/admin/booking/{id}/delete",name="admin_booking_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function delete(Booking $booking,EntityManagerInterface $entityManager)
    {
        $id = $booking->getId();

        $entityManager->remove($booking);
        $entityManager->flush();

        $this->addFlash("success","La réservation n°$id a bien été supprimée !");
        return $this->redirectToRoute('admin_bookings');
    }
}

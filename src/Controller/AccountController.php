<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\OrderRepository;
use App\Repository\BookingRepository;
use App\Repository\CommentRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AccountController extends AbstractController
{
    /**
     * Permet d'afficher une page de connexion
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();

        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]
        );
    }

    /**
     * Permet de se déconnecter
     * @Route("/logout",name="account_logout")
     *
     * @return void
     */
    public function logout()
    {
        // besoin de rien tout se passe via le fichier security.yaml
    }

    /**
     * Permet d'afficher une page 'S'inscrire'
     * @Route ("/register",name="account_register")
     *
     * @return Response
     */
    public function register(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $entityManager)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user,$user->getHash());

            // on modifie le mdp avec le setter
            $user->setHash($hash);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success","Votre compte a bien été créé, veuillez désormais vous connecter");

            return $this->RedirectToRoute("account_login");
        }

        return $this->render('account/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Vue et modification du profil utilisateur
     *
     * @Route("/account/profile",name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request,EntityManagerInterface $entityManager,BookingRepository $bookingRepo,OrderRepository $orderRepo,CommentRepository $CommentRepo)
    {
        $user = $this->getUser();

        $bookings = $bookingRepo->findBy(['user'=>$user]);
        $orders = $orderRepo->findBy(['user'=>$user]);
        $comments = $CommentRepo->findBy(['author'=>$user]);

        $form = $this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success","Les informations de votre profil ont bien été modifiées.");
        }

        if(isset($_POST) && isset($_POST['content']) && isset($_POST['rating']) && isset($_POST['booking_id']))
        {
            $comment = new Comment();

            $content = $_POST['content'];
            $rating = intval($_POST['rating']);
            $bookingId = intval($_POST['booking_id']);

            $booking = $bookingRepo->findOneBy([
                'id' => $bookingId
            ]);

            $comment->prePersist();

            $comment->setRating($rating)
                    ->setContent($content)
                    ->setAuthor($this->getUser())
                    ->setBooking($booking)
            ;

            $entityManager->persist($comment);
            $entityManager->flush();
        
            $this->addFlash("success_comment","Votre avis sur ce repas a bien été pris en compte.");
        }

        return $this->render('account/profile.html.twig',
        [
            'form' => $form->createView(),
            'bookings' => $bookings,
            'orders' => $orders,
            'comments' => $comments
        ]);
    }

    /**
     * Suppression d'une réservation par le client
     * @Route("/booking/{id}/delete",name="booking_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function deleteBooking(Booking $booking,EntityManagerInterface $entityManager)
    {
        $id = $booking->getId();

        $entityManager->remove($booking);
        $entityManager->flush();

        $this->addFlash("success","La réservation n°$id a bien été supprimée !");
        return $this->redirectToRoute('account_profile');
    }

    /**
     * Suppression d'une ligne de commande par le client
     * @Route("/order/{id}/delete",name="order_delete")
     * @param Order $order
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function deleteOrder(Order $order,EntityManagerInterface $entityManager)
    {
        $id = $order->getId();
        $status = $order->getStatus();

        if($status == "commandé")
        {
            $entityManager->remove($order);
            $entityManager->flush();
            
            $this->addFlash("success","La ligne de commande $id a bien été supprimée !");
        }
        else
        {
            $this->addFlash("warning","La ligne de commande $id ne peut pas être supprimée car elle a déjà été payée et est en attente de livraison !");
        }

        
        return $this->redirectToRoute('admin_orders');
    }
}

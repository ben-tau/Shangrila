<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

            $this->addFlash("success","Votre compte a bien été créé");

            return $this->RedirectToRoute("account_login");
        }

        return $this->render('account/register.html.twig',[
            'form' => $form->createView()
        ]);
    }
}

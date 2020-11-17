<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    /**
     * @Route("/menus", name="menus")
     */
    public function index(MenuRepository $repo,Request $request,EntityManagerInterface $entityManager): Response
    {
        $menus = $repo->findAll();

        $order = new Order();

        if(isset($_POST) && isset($_POST['quantity']) && isset($_POST['title']) && isset($_POST['price']))
        {
            $qty = (int) $_POST['quantity'];
            $title = $_POST['title'];
            $price = (float) $_POST['price'];
            $total = $price * $qty;

            $order->setUser($this->getUser())
                  ->setQuantity($qty)
                  ->setTotal($total)
                  ->setTitle($title)
                  ->setStatus("commandé")
            ;

            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash("success","Votre commande de ".$qty."x \"$title\" a bien été ajoutée au panier repas");

        }

        return $this->render('menu/index.html.twig', [
            'menus' => $menus
        ]);
    }
}

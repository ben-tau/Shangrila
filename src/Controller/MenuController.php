<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\Comment;
use App\Form\OrderType;
use App\Repository\MenuRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    /**
     * @Route("/menus", name="menus")
     */
    public function index(MenuRepository $menuRepo,CommentRepository $commentRepo,Request $request,EntityManagerInterface $entityManager): Response
    {
        $menus = $menuRepo->findAll();
        $comments = $commentRepo->findAll();

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

        if(isset($_POST) && isset($_POST['content']) && isset($_POST['rating']) && isset($_POST['menu_id']))
        {
            $comment = new Comment();

            $content = $_POST['content'];
            $rating = intval($_POST['rating']);
            $menuId = intval($_POST['menu_id']);

            $menu = $menuRepo->findOneBy([
                'id' => $menuId
            ]);

            $comment->prePersist();

            $comment->setRating($rating)
                    ->setContent($content)
                    ->setAuthor($this->getUser())
                    ->setMenu($menu)
            ;

            $entityManager->persist($comment);
            $entityManager->flush();
        
            $this->addFlash("success","Votre commentaire a bien été posté et sera soumi à modération par un administrateur");
        }

        return $this->render('menu/index.html.twig', [
            'menus' => $menus,
            'comments' => $comments
        ]);
    }
}

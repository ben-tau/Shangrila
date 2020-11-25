<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminOrderController extends AbstractController
{
    /**
     * @Route("/admin/orders", name="admin_orders")
     */
    public function index(OrderRepository $orderRepo): Response
    {
        $orders = $orderRepo->findAll();

        return $this->render('admin/order/index.html.twig', [
            'orders'=>$orders
        ]);
    }

    /**
     * Suppression d'une ligne de commande par l'admin
     * @Route("/admin/order/{id}/delete",name="admin_order_delete")
     * @param Order $order
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function delete(Order $order,EntityManagerInterface $entityManager)
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

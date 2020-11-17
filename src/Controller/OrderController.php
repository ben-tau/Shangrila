<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();
        $userId = $this->getUser()->getId();

        $order = $orderRepo->findBy([
            'user' => $userId
        ]);

        $total = $orderRepo->findTotalPriceOfOrders($userId);
        $sum = $total[0]["totalPrice"];
        $total = $sum;

        return $this->render('order/index.html.twig', [
            'order' => $order,
            'user' => $user,
            'total' => $total
        ]);
    }
}

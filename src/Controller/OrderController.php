<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(OrderRepository $orderRepo,Request $request,EntityManagerInterface $entityManager,FrToDateTimeTransformer $transformer): Response
    {
        $user = $this->getUser();
        $userId = $this->getUser()->getId();

        $order = $orderRepo->findBy([
            'user' => $userId,
            'status' => 'commandé'
        ]);  
        
        $total = $orderRepo->findTotalPriceOfOrders($userId);
        $sum = $total[0]["totalPrice"];
        $total = $sum;

        if(isset($_POST) && isset($_POST['deliveryDay']) && isset($_POST['deliveryTime']))
        {
            $payment = new Payment();

            $deliveryDate = $_POST['deliveryDay'].' '.$_POST['deliveryTime'];
            $formatedDeliveryDate = $transformer->reverseTransform($deliveryDate);

            $amount = $total;
            $token = random_int(10000,99999);
            $orderLinesArray = $orderRepo->findAllTitlesOfOrders($userId);
            $content = [];
            $status = "en attente de paiement";

            foreach($orderLinesArray as $key=>$array)
            {
                if(is_array($array))
                {
                    foreach($array as $name=>$value)
                    {
                        array_push($content,$value);  
                    }
                }
            }

            $payment->setAmount($amount)
                    ->setToken($token)
                    ->setContent($content)
                    ->setStatus($status)
                    ->setUser($this->getUser())
                    ->setDeliveryDate($formatedDeliveryDate)
            ;

            $entityManager->persist($payment);
            $entityManager->flush();

            $paymentId = $payment->getId();
            $status = "'commandé'";

            $orderRepo->LinkTheOrderLineAndThePayment($userId,$paymentId,$status);

            return $this->redirectToRoute("order_payment",
            [
                'payment'=>$payment
            ]);
        }



        return $this->render('order/index.html.twig', [
            'order' => $order,
            'user' => $user,
            'total' => $total
        ]);
    }
}

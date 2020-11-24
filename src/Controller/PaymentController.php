<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Payment;
use Stripe\PaymentIntent;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    /**
     * @Route("/order/payment", name="order_payment")
     */
    public function index(PaymentRepository $repo,EntityManagerInterface $entityManager,FrToDateTimeTransformer $transformer): Response
    {   
        $user = $this->getUser();
        $status = "'en attente de paiement'";
        $payed = "'paiement effectué'";
        $deliveryDate = "";

        $paymentContent = $repo->findContentOfLastPaymentNotPayedYet($user,$status);

        if(!empty($paymentContent))
        {
            $max = intval(max(array_keys($paymentContent[0]["totalContent"]))/2);
            $paymentContent = $paymentContent[0]["totalContent"];
            $totalAmount = $repo->findAmountOfLastPaymentNotPayedYet($user,$status);
            $amount = $totalAmount[0]["totalAmount"];
            
            // on instancie stripe
            \Stripe\Stripe::setApiKey('sk_test_51HMweMJjakv6Wiwjc2upl76KsCIMoKO2oofCqpKgJYMFXmkaIqqelsiUL2OObiKK8gFt9VpfgAbz9kzwV9GJqn3b00d2lIVQEs');

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount*100,
                'currency' => 'eur'
            ]);

            $secret = $intent['client_secret'];
        }
        else
        {
            $max = 0;
            $secret = "";
            $amount = 0;
        }

        if(isset($_POST) && !empty($_POST))
        {
            if(isset($_POST["name"]) && !empty($_POST["name"]) &&
                isset($_POST["email"]) && !empty($_POST["email"]) &&
                isset($_POST["amount"]) && !empty($_POST["amount"]))
            {
                $totalAddress = $user->getTotalAddress();

                $repo->UpdateStatusPaymentToPayed($user,$payed);
                
                $paymentContent = "";
                $max = 0;
                $amount = 0;

                $orders = $user->getOrders();

                foreach($orders as $order)
                {
                    $order->setStatus("payé, en attente de livraison");
                }
                   
                $entityManager->persist($order);
                $entityManager->flush();
                
                $formatedDeliveryDate = $repo->findDeliveryDateOfLastPayedPayment($user,$payed);

                $deliveryDateArray = (array) $formatedDeliveryDate[0]["deliveryDate"];
                $deliveryDate = $deliveryDateArray["date"];

                $this->addFlash("success","Votre paiement a bien été effectué ! Votre Panier Repas sera livré au : $totalAddress");
            }
            else
            {
                $this->addFlash("danger","Un problème est survenu au cours du paiement de la commande. Veuillez réessayer, sinon contactez-nous par téléphone ou par e-mail");
            }
        }
        
        
        return $this->render('order/payment.html.twig', [
            'paymentContent'=>$paymentContent,
            'secret'=>$secret,
            'amount'=>$amount,
            'max'=>$max,
            'deliveryDate'=>$deliveryDate
        ]);
    }
}

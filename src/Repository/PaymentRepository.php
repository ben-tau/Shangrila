<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    // /**
    //  * @return Payment[] Returns an array of Payment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAmountOfLastPaymentNotPayedYet($id,$status)
    {
        return $this->createQueryBuilder('p')
                    ->select('p.amount as totalAmount')
                    ->where('p.user = :user_id')
                    ->andWhere("p.status = $status")
                    ->setParameter('user_id',$id)
                    ->getQuery()
                    ->getResult()
        ;
    }

    public function findContentOfLastPaymentNotPayedYet($id,$status)
    {
        return $this->createQueryBuilder('p')
                    ->select('p.content as totalContent')
                    ->where('p.user = :user_id')
                    ->andWhere("p.status = $status")
                    ->setParameter('user_id',$id)
                    ->getQuery()
                    ->getResult()
        ;
    }

    public function UpdateStatusPaymentToPayed($id,$payed)
    {
        $q = $this->createQueryBuilder('p')
                  ->update()
                  ->set('p.status',$payed)
                  ->where('p.user = :user_id')
                  ->setParameter('user_id',$id)
        ;

        $query = $q->getQuery();
        return $query->execute();

    }

    public function findDeliveryDateOfLastPayedPayment($id,$payed)
    {
        return $this->createQueryBuilder('p')
                    ->select('p.deliveryDate as deliveryDate')
                    ->where('p.user = :user_id')
                    ->andWhere("p.status = $payed")
                    ->setParameter('user_id',$id)
                    ->getQuery()
                    ->getResult()
        ;            
    }
}

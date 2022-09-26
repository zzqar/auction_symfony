<?php

namespace App\Repository;

use App\Entity\Goods;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $good
     * @return string Returns an array of Transaction objects
     */
    public function findByMaxBetForGood($good): string
    {

        $b =  $this->createQueryBuilder('t')
            ->Select('MAX(t.pay)')
            ->andWhere('t.good_id = :good')
            ->setParameter('good', $good)
            ->getQuery()
            ->getResult()
        ;
            if(is_null($b[0][1])){
                $b[0][1] ='0';
            }



        return  $b[0][1];
    }


    public function findByMaxBetForGoodAndUser( User $user ,Goods $good):  string
    {

         $b = $this->createQueryBuilder('t')
            ->Select('MAX(t.pay) ')
            ->andWhere(' t.good_id = :good')
            ->andWhere(' t.user_id = :user')
            ->setParameters([
                'good'=>$good->getId(),
                'user'=>$user->getId()
            ])
            ->getQuery()
             ->getResult()
         ;
        if(is_null($b[0][1])){
            $b[0][1] ='0';
        }
        return $b[0][1];



    }



//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

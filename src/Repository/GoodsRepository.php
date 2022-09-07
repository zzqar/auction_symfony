<?php

namespace App\Repository;

use App\Entity\Goods;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Goods>
 *
 * @method Goods|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goods|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goods[]    findAll()
 * @method Goods[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoodsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Goods::class);
    }

    public function add(Goods $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Goods $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function countOldGoods(): int
    {
        return $this->getOldGoodsQueryBuilder()->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
    }




    private function getOldGoodsQueryBuilder(): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status ')
            ->andWhere('c.last_date <= :date')
            ->setParameters([
                'status' => '0',

                'date' => new \DateTime(),
            ])
        ;
    }

    /**
     * @return Goods[] Returns an array of Goods objects
     */
    public function getOldGoods(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status ')
            ->andWhere('c.last_date <= :date')
            ->setParameters([
                'status' => '0',

                'date' => new \DateTime(),
            ])
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Goods
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

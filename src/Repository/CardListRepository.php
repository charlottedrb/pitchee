<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\CardList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CardList|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardList|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardList[]    findAll()
 * @method CardList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardList::class);
    }

    /**
     * @return CardList[] Returns an array of CardList objects
     */

    public function findByUser($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return CardList[] Returns an array of Card objects
     */

    public function findByParent($value): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.parent = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return CardList[] Returns an array of CardList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CardList
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

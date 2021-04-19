<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    /**
     * @return Card[] Returns an array of Card objects
     */
    
    public function findByUser($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllButLiked($userId): array
    {
        $qb = $this->createQueryBuilder('d');

        $likes = $qb->select('like')
            ->from(Like::class, 'hfifr')
            ->where($qb->expr()->eq('like.card_id', 1))
            ->where($qb->expr()->eq('like.user_id', $userId));



        return $qb->select('id')
            ->from(Card::class, 'cd')
            ->where($qb->expr()->notIn('card.id', $likes->getDQL()))
            ->getQuery()
            ->getResult();

//        $likes = $this->createQueryBuilder('d')
//            ->select('card.id')
//            ->join('card.id', 'ca')
//            ->where('ca.id')
//        return [];
    }
    

    // /**
    //  * @return Card[] Returns an array of Card objects
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
    public function findOneBySomeField($value): ?Card
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

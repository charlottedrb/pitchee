<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function findByCardsButLiked($userId): array
    {
        $end = new \DateTimeImmutable('-7 days');

        return $this->createQueryBuilder('l')
            ->addSelect('m')
            ->leftJoin('l.card', 'm')
            ->andWhere('l.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByCardsButLikedWeekOld($userId): array
    {
        $end = new \DateTimeImmutable('-7 days');

        return $this->createQueryBuilder('l')
            ->addSelect('m')
            ->leftJoin('l.card', 'm')
            ->andWhere('l.user = :userId')
            ->setParameter('userId', $userId)
            ->andWhere('createdAt between NOW() and :end')
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function findAllByUser($userId): array
    {
        return $this->createQueryBuilder('n')
            ->addSelect('m')
            ->leftJoin('n.card', 'm')
            ->andWhere('n.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('n.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Like[] Returns an array of Like objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    // /**
    //  * @return Like[] Returns an array of Like objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Like
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

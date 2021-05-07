<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Like;
use DateTimeZone;
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

    public function findByLiked($userId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select * from card c
            where id in (
                select card_id from `like` l 
                where user_id = :userId and l.liked = true
            )
            order by created_at ASC
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $userId]);

        return $stmt->fetchAllAssociative();
    }

    public function findByButLiked($userId): array
    {
        // utilisation de cette méthode pour le return en tableau -> plus pratique pour le JSON
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select * from card c
            where id not in (
                select card_id from `like` l 
                where user_id = :userId
            )
            and user_id != :userId
            order by created_at ASC
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $userId]);

        return $stmt->fetchAllAssociative();

        /*$qb = $this->createQueryBuilder('d');
        $likes = $this->createQueryBuilder('l')
            ->addSelect('l.id')
            ->where('l.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
        return $qb
            ->where($qb->expr()->notIn('d.id', $likes))
            ->getQuery()
            ->getResult();*/
    }

    public function findByCardsButLikedWeekOld($userId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $tz = new DateTimeZone("europe/paris");
        $now = new \DateTimeImmutable('+1 day', $tz);
//        dd($now);
        $end = new \DateTimeImmutable('-7 days', $tz);
//       dd($end);

        $sql = '
            select * from card c
            where id not in (
                select card_id from `like` l 
                where user_id = :userId
            )
            and created_at between :endDate and :nowDate
            and user_id != :userId
            order by created_at ASC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'endDate' => $end->format('Y-m-d'),
            'nowDate' => $now->format('Y-m-d')
        ]);

        return $stmt->fetchAllAssociative();
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
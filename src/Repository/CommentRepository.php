<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
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
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // public function findComments($trick_id, $first_value, $max): ?Comment
    // {
    //     return $this->createQueryBuilder('c')
    //         ->select('c.trick')
    //         ->andWhere('c.trick =:val')
    //         ->setParameter('val', $trick_id)
    //         // ->orderBy('c.createdAt', 'DESC')
    //         // ->setFirstResult($first_value)
    //         // ->setMaxResults($max)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    public function findComments($trick_id, $limit, $offset): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT c.contenu, c.created_at, u.email FROM comment c
            inner join 
            user u
            on u.id = c.user_id
            WHERE c.trick_id = :trick_id
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['trick_id' => $trick_id]);
        // $stmt->execute(['price' => $price]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }
}

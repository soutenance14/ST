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

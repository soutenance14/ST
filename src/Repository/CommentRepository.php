<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    public function findComments($trickId, $offset, $limit)
    {
        return $this->createQueryBuilder('c')
            ->select('c.contenu, c.createdAt, u.username')
            ->innerJoin('App\Entity\user', 'u', Join::WITH, 'u = c.user')
            ->innerJoin('App\Entity\trick', 't', Join::WITH, 't = c.trick')
            ->andWhere('t.id = :val')
            ->setParameter('val', $trickId)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

}

<?php

namespace App\Repository;

use App\Entity\DialogUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DialogUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method DialogUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method DialogUsers[]    findAll()
 * @method DialogUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DialogUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DialogUsers::class);
    }


    public function getInterlocutor($dialog, $user)
    {
        return $this->createQueryBuilder('du')
            ->where('du.dialog = :dialog')
            ->setParameter('dialog', $dialog)
            ->andWhere('du.user != :user')
            ->setParameter('user', $user)
            ->getQuery()->getResult();
    }
    // /**
    //  * @return DialogUsers[] Returns an array of DialogUsers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DialogUsers
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

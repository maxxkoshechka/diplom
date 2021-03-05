<?php

namespace App\Repository;

use App\Entity\Dialogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dialogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dialogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dialogs[]    findAll()
 * @method Dialogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DialogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dialogs::class);
    }

    public function findAllByUser($user){
        $query = $this->createQueryBuilder('d')
            ->Join('d.dialogUsers', 'du', Join::WITH, 'du.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        return $query;
    }
    // /**
    //  * @return Dialogs[] Returns an array of Dialogs objects
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
    public function findOneBySomeField($value): ?Dialogs
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

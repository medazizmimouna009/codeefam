<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 */
class OffreRepository extends ServiceEntityRepository 
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    public function findByPrix($prix): array
{
    return $this->createQueryBuilder('o')  // 'o' is the alias for 'Offre' entity
        ->where('o.prix LIKE :prix')  // Search for prices similar to the given value
        ->setParameter('prix', '%' . $prix . '%')  // Add '%' for LIKE query
        ->orderBy('o.id', 'ASC')  // Order by id in ascending order
        ->getQuery()
        ->getResult();
}

    //    /**
    //     * @return Offre[] Returns an array of Offre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Offre
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }


    // src/Repository/ReponseRepository.php
public function findAllWithQuestions()
{
    return $this->createQueryBuilder('r')
        ->leftJoin('r.question', 'q')
        ->addSelect('q')
        ->getQuery()
        ->getResult();
}
}
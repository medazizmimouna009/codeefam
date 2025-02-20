<?php
// src/Repository/QuizRepository.php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findBySearchAndCours(?string $search, ?int $coursId): array
    {
        $qb = $this->createQueryBuilder('q');

        if ($search) {
            $qb->andWhere('q.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($coursId !== null) {
            $qb->andWhere('q.nomCours = :coursId')
               ->setParameter('coursId', $coursId);
        }

        return $qb->getQuery()->getResult();
    }
}
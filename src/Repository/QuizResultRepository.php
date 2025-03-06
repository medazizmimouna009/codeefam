<?php

namespace App\Repository;

use App\Entity\QuizResult;
use App\Entity\User;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizResult::class);
    }

    /**
     * Récupère les résultats d'un utilisateur pour un quiz spécifique.
     *
     * @param User $user L'utilisateur concerné.
     * @param Quiz $quiz Le quiz concerné.
     * @return QuizResult[] Les résultats correspondants.
     */
    public function findResultsByUserAndQuiz(User $user, Quiz $quiz): array
    {
        return $this->createQueryBuilder('qr')
            ->andWhere('qr.user = :user')
            ->andWhere('qr.quiz = :quiz')
            ->setParameter('user', $user)
            ->setParameter('quiz', $quiz)
            ->orderBy('qr.createdAt', 'DESC') // Optionnel : trier par date de création
            ->getQuery()
            ->getResult();
    }
}
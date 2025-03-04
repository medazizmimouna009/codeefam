<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findByCoursAndNiveau(?string $niveau = null, ?int $nomCoursId = null)
    {
        $qb = $this->createQueryBuilder('q');
    
        if ($niveau) {
            $qb->andWhere('q.niveau = :niveau')
               ->setParameter('niveau', $niveau);
        }
    
        if ($nomCoursId) {
            $qb->andWhere('q.nomCours = :nomCoursId')
               ->setParameter('nomCoursId', $nomCoursId);
        }
    
        return $qb->getQuery();
    }
    // Dans votre QuizRepository
public function findQuizResultsByUser(User $user)
{
    return $this->createQueryBuilder('q')
        ->leftJoin('q.quizResults', 'qr')
        ->andWhere('qr.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}





































    /**
     * Forcefully update a quiz and its associated questions.
     *
     * @param Quiz $quiz The quiz entity to update.
     * @param array $data The form data (nom, niveau, nomCours, questions).
     */
    public function forceUpdateQuiz(Quiz $quiz, array $data): void
    {
        $entityManager = $this->getEntityManager();

        // Step 1: Update basic fields
        $quiz->setNom($data['nom']);
        $quiz->setNiveau($data['niveau']);
        $quiz->setNomCours($data['nomCours']);

        // Step 2: Clear existing questions
        $quiz->getQuestions()->clear();

        // Step 3: Add new questions
        foreach ($data['questions'] as $questionId) {
            $question = $entityManager->getRepository(Question::class)->find($questionId);
            if ($question) {
                $quiz->addQuestion($question);
            }
        }

        // Step 4: Persist and flush
        $entityManager->persist($quiz);
        $entityManager->flush();
    }
}
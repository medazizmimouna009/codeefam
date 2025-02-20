<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\NomCours;
use App\Entity\Quiz;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findRandomQuestionsByCours(int $coursId, int $limit = 5): array
    {
        // Option 1: Using Doctrine Extensions (MySQL specific, if configured correctly)
        // This requires you to have the Doctrine Extensions bundle installed and RAND() configured in doctrine.yaml

        $queryBuilder = $this->createQueryBuilder('q')
            ->andWhere('q.nomCours = :coursId')
            ->setParameter('coursId', $coursId)
            ->orderBy('RAND()')
            ->setMaxResults($limit);

        try {
            return $queryBuilder->getQuery()->getResult();
        } catch (\Doctrine\ORM\Query\QueryException $e) {
            // Handle the exception if RAND() is not recognized (e.g., log it)
            error_log("DQL error: " . $e->getMessage()); // Log the error
            // Fallback to option 2 or other error handling.
            return $this->findRandomQuestionsByCoursInPhp($coursId, $limit); // Try the PHP shuffle method as a fallback
        }


        // Option 2: Fetch all questions for the cours and randomize in PHP (Less efficient for large datasets)
    }

    // Fallback function when RAND() is not supported in DQL.
    private function findRandomQuestionsByCoursInPhp(int $coursId, int $limit = 5): array
    {
        $questions = $this->findBy(['nomCours' => $coursId]);

        shuffle($questions); // Randomize the order of questions
        return array_slice($questions, 0, $limit); // Take the first X questions after shuffling
    }
    public function findBySearchAndCourse(?string $search, ?int $courseId): array
    {
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.nomCours', 'c');
    
        // Filtre par texte de recherche
        if ($search) {
            $qb->andWhere('q.texte LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
    
        // Filtre par ID de cours
        if ($courseId !== null) {
            // Ensure $courseId is an integer
            $qb->andWhere('c.id = :courseId')
               ->setParameter('courseId', $courseId);
        }
    
        return $qb->getQuery()->getResult();
    }


    public function updateQuestionsWithQuiz(int $quizId, array $questionIds): void
    {
        if (empty($questionIds)) {
            return; // No questions to update
        }
    
        // Prepare SQL query to update quiz_id for the selected questions
        $query = $this->_em->getConnection()->prepare("
            UPDATE question 
            SET quiz_id = :quizId 
            WHERE id IN (:questionIds)
        ");
    
        // Convert array to a comma-separated string for SQL IN clause
        $questionIdsString = implode(',', $questionIds);
    
        // Execute the query with parameters
        $query->execute([
            'quizId' => $quizId,
            'questionIds' => $questionIdsString,
        ]);
    }
    
    public function findQuestionsWithReponsesByQuizId(int $quizId): array
    {
        $conn = $this->getEntityManager()->getConnection();
    
        $sql = '
            SELECT 
                q.id AS question_id,
                q.texte AS question_texte,
                r.id AS reponse_id,
                r.texte AS reponse_texte,
                r.est_correcte AS reponse_est_correcte
            FROM question q
            INNER JOIN quiz_question qq ON q.id = qq.question_id
            INNER JOIN quiz z ON qq.quiz_id = z.id
            LEFT JOIN reponse r ON r.question_id = q.id
            WHERE z.id = :quizId
            ORDER BY q.id, r.id
        ';
    
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('quizId', $quizId, \PDO::PARAM_INT);
        $resultSet = $stmt->executeQuery();
    
        $results = $resultSet->fetchAllAssociative();
    
        if (empty($results)) {
            error_log('Aucune réponse trouvée pour le quiz ID: ' . $quizId);
        } else {
            error_log('Résultats récupérés: ' . print_r($results, true));
        }
    
        return $results;
    }
    
    


}

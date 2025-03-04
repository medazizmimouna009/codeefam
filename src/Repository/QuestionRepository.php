<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Récupère les questions en fonction du niveau et du cours.
     *
     * @param string $niveau Le niveau de difficulté (facile, moyen, difficile)
     * @param int|null $nomCoursId L'ID du cours (peut être null)
     * @return Question[] Retourne un tableau de questions
     */
    public function findByNiveauAndNomCourscours(string $niveau, ?int $nomCoursId): array
    {
        // Crée un QueryBuilder pour construire la requête
        $qb = $this->createQueryBuilder('q')
            ->where('q.niveau = :niveau') // Filtre par niveau
            ->setParameter('niveau', $niveau);

        // Si un ID de cours est fourni, filtre également par cours
        if ($nomCoursId) {
            $qb->andWhere('q.nomCours = :nomCours')
                ->setParameter('nomCours', $nomCoursId);
        }

        // Exécute la requête et retourne les résultats
        return $qb->getQuery()->getResult();
    }
















    public function findByNiveauAndNomCours(?string $niveau, ?int $nomCoursId): QueryBuilder
{
    // Crée un QueryBuilder pour construire la requête
    $qb = $this->createQueryBuilder('q');

    // Ajouter le filtre par niveau si fourni
    if ($niveau !== null && $niveau !== '') {
        $qb->andWhere('q.niveau = :niveau')
           ->setParameter('niveau', $niveau);
    }

    // Ajouter le filtre par cours si fourni
    if ($nomCoursId !== null && $nomCoursId !== '') {
        $qb->andWhere('q.nomCours = :nomCoursId')
           ->setParameter('nomCoursId', $nomCoursId);
    }

    // Retourner le QueryBuilder
    return $qb;
}
}
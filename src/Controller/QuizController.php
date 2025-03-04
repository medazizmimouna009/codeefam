<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;


use App\Entity\Quiz;
use App\Entity\NomCours;
use Knp\Component\Pager\PaginatorInterface; // Importer le service PaginatorInterface
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Repository\NomCoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Reponse;
use App\Entity\Question;
use App\Entity\UserReponse;
use App\Repository\QuestionRepository;
use App\Form\EditQuizType;
use App\Entity\QuizResult;
use App\Repository\QuizResultRepository;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/quiz')]
class QuizController extends AbstractController
{
    

   #[Route('/', name: 'app_quiz_index', methods: ['GET'])]
public function index(
    Request $request,
    QuizRepository $quizRepository,
    NomCoursRepository $nomCoursRepository,
    PaginatorInterface $paginator
): Response {
    // Récupérer les paramètres de filtrage
    $coursId = $request->query->get('cours');
    $niveau = $request->query->get('niveau');

    // Convertir $coursId en entier (ou null si vide)
    $nomCoursId = $coursId !== null && $coursId !== '' ? (int)$coursId : null;

    // Récupérer les quiz filtrés en utilisant la méthode findByCoursAndNiveau
    $query = $quizRepository->findByCoursAndNiveau($niveau, $nomCoursId);

    // Paginer les résultats
    $quizzes = $paginator->paginate(
        $query, // Requête filtrée
        $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
        1 // Nombre d'éléments par page (ajustez selon vos besoins)
    );

    // Récupérer tous les cours pour le filtre
    $nomCours = $nomCoursRepository->findAll();

    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    return $this->render('quiz/index.html.twig', [
        'quizzes' => $quizzes,
        'nomCours' => $nomCours,
        'selectedCours' => $coursId, // Conserver la sélection du filtre de cours
        'selectedNiveau' => $niveau, // Conserver la sélection du filtre de niveau
        'user' => $user, // Passer l'utilisateur à la vue
    ]);
}



#[Route('/quiz/list', name: 'app_quiz_list', methods: ['GET'])]
public function list(
    Request $request,
    QuizRepository $quizRepository,
    NomCoursRepository $nomCoursRepository,
    PaginatorInterface $paginator,
    EntityManagerInterface $entityManager
): Response {
    // Récupérer les paramètres de filtrage
    $coursId = $request->query->get('cours');
    $niveau = $request->query->get('niveau');

    // Convertir $coursId en entier (ou null si vide)
    $nomCoursId = $coursId !== null && $coursId !== '' ? (int)$coursId : null;

    // Récupérer les quiz filtrés en utilisant la méthode findByCoursAndNiveau
    $query = $quizRepository->findByCoursAndNiveau($niveau, $nomCoursId);

    // Paginer les résultats
    $quizzes = $paginator->paginate(
        $query, // Requête filtrée
        $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
        1 // Nombre d'éléments par page (ajustez selon vos besoins)
    );

    // Récupérer tous les cours pour le filtre
    $nomCours = $nomCoursRepository->findAll();

    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    // Récupérer les scores et l'état de participation de l'utilisateur pour chaque quiz
    $userScores = [];
    $hasParticipated = [];
    foreach ($quizzes as $quiz) {
        $quizId = $quiz->getId();
        $userReponses = $entityManager->getRepository(UserReponse::class)->findBy([
            'quiz' => $quizId,
            'user' => $user,
        ]);

        // Calculer le score de l'utilisateur pour ce quiz
        $score = 0;
        foreach ($userReponses as $userReponse) {
            if ($userReponse->isEstCorrecte()) {
                $score++;
            }
        }

        $userScores[$quizId] = $score;

        // Vérifier si l'utilisateur a déjà participé à ce quiz (peu importe le score)
        $hasParticipated[$quizId] = count($userReponses) > 0; // True si l'utilisateur a soumis des réponses
    }

    return $this->render('quiz/list.html.twig', [
        'quizzes' => $quizzes,
        'nomCours' => $nomCours,
        'selectedCours' => $coursId, // Conserver la sélection du filtre de cours
        'selectedNiveau' => $niveau, // Conserver la sélection du filtre de niveau
        'user' => $user, // Passer l'utilisateur à la vue
        'userScores' => $userScores, // Passer les scores de l'utilisateur à la vue
        'hasParticipated' => $hasParticipated, // Passer l'état de participation à la vue
    ]);
}



 // src/Controller/QuizController.php// src/Controller/QuizController.php

#[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, QuestionRepository $questionRepository): Response
{
    $quiz = new Quiz();
    $form = $this->createForm(QuizType::class, $quiz);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Get the selected question IDs from the request
        $selectedQuestionIds = $request->request->all()['quiz']['questions'] ?? [];

        // Fetch the selected questions from the database
        $selectedQuestions = $questionRepository->findBy(['id' => $selectedQuestionIds]);

        // Assign the selected questions to the quiz
        foreach ($selectedQuestions as $question) {
            $quiz->addQuestion($question);
        }

        // Save the quiz
        $entityManager->persist($quiz);
        $entityManager->flush();

        $this->addFlash('success', 'Le quiz a été créé avec succès.');
        return $this->redirectToRoute('app_quiz_index');
    }

    return $this->render('quiz/new.html.twig', [
        'quiz' => $quiz,
        'form' => $form->createView(),
    ]);
}

#[Route('/quiz/{id}', name: 'app_quiz_show', methods: ['GET'])]
public function show(Quiz $quiz): Response
{
    return $this->render('quiz/show.html.twig', [
        'quiz' => $quiz,
    ]);
}


#[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager, QuestionRepository $questionRepository): Response
{
    $form = $this->createForm(QuizType::class, $quiz);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les IDs des questions sélectionnées depuis le formulaire
        $selectedQuestionIds = $request->request->all()['quiz']['questions'] ?? [];

        // Si $selectedQuestionIds est une chaîne, la convertir en tableau
        if (is_string($selectedQuestionIds)) {
            $selectedQuestionIds = explode(',', $selectedQuestionIds);
        }

        // Récupérer les questions correspondantes depuis la base de données
        $selectedQuestions = $questionRepository->findBy(['id' => $selectedQuestionIds]);

        // Supprimer les questions qui ne sont plus sélectionnées
        foreach ($quiz->getQuestions() as $question) {
            if (!in_array($question->getId(), $selectedQuestionIds)) {
                $quiz->removeQuestion($question);
            }
        }

        // Ajouter les nouvelles questions sélectionnées
        foreach ($selectedQuestions as $question) {
            if (!$quiz->getQuestions()->contains($question)) {
                $quiz->addQuestion($question);
            }
        }

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        $this->addFlash('success', 'Le quiz a été modifié avec succès.');
        return $this->redirectToRoute('app_quiz_index');
    }

    return $this->render('quiz/edit.html.twig', [
        'quiz' => $quiz,
        'form' => $form->createView(),
    ]);
}



#[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
        // Supprimer toutes les UserReponse qui référencent ce quiz
        $userReponses = $entityManager->getRepository(UserReponse::class)->findBy(['quiz' => $quiz]);
        foreach ($userReponses as $userReponse) {
            $entityManager->remove($userReponse);
        }

        // Supprimer tous les QuizResult qui référencent ce quiz
        $quizResults = $entityManager->getRepository(QuizResult::class)->findBy(['quiz' => $quiz]);
        foreach ($quizResults as $quizResult) {
            $entityManager->remove($quizResult);
        }

        // Supprimer le quiz
        $entityManager->remove($quiz);
        $entityManager->flush();

        $this->addFlash('success', 'Le quiz a été supprimé avec succès.');
    } else {
        $this->addFlash('error', 'Token CSRF invalide.');
    }

    return $this->redirectToRoute('app_quiz_index');
}



  
#[Route('/quiz/{id}/play', name: 'quiz_play', methods: ['GET', 'POST'])]
public function play(Quiz $quiz, Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer les questions du quiz avec leurs réponses
    $questions = $quiz->getQuestions();

    // Créer un formulaire pour les réponses
    $form = $this->createFormBuilder()->getForm();

    // Traiter la soumission du formulaire
    if ($request->isMethod('POST')) {
        $data = $request->request->all();

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Enregistrer les réponses de l'utilisateur
        foreach ($questions as $question) {
            $reponseId = $data['question_' . $question->getId()] ?? null;

            if ($reponseId) {
                $reponse = $entityManager->getRepository(Reponse::class)->find($reponseId);

                if ($reponse) {
                    $userReponse = new UserReponse();
                    $userReponse->setQuiz($quiz);
                    $userReponse->setUser($user);
                    $userReponse->setQuestion($question);
                    $userReponse->setReponse($reponse);
                    $userReponse->setEstCorrecte($reponse->isEstCorrecte());

                    $entityManager->persist($userReponse);
                }
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Vos réponses ont été enregistrées avec succès.');
        return $this->redirectToRoute('quiz_results', ['id' => $quiz->getId()]);
    }

    return $this->render('quiz/play.html.twig', [
        'quiz' => $quiz,
        'questions' => $questions,
        'form' => $form->createView(),
    ]);
}

#[Route('/quiz/{id}/results', name: 'quiz_results', methods: ['GET'])]
public function results(Quiz $quiz, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    // Récupérer les réponses de l'utilisateur pour ce quiz
    $userReponses = $entityManager->getRepository(UserReponse::class)->findBy([
        'quiz' => $quiz,
        'user' => $user,
    ]);

    // Calculer le score
    $score = 0;
    foreach ($userReponses as $userReponse) {
        if ($userReponse->isEstCorrecte()) {
            $score++;
        }
    }

    // Calculer le nombre total de questions
    $totalQuestions = count($quiz->getQuestions());

    // Enregistrer le résultat dans la base de données
    $quizResult = new QuizResult();
    $quizResult->setQuiz($quiz);
    $quizResult->setUser($user);
    $quizResult->setScore($score);
    $quizResult->setTotalQuestions($totalQuestions);

    $entityManager->persist($quizResult);
    $entityManager->flush();

    // Compter le nombre de tentatives
    $attempts = $entityManager->getRepository(QuizResult::class)->count([
        'quiz' => $quiz,
        'user' => $user,
    ]);

    // Vérifier si l'utilisateur a déjà participé à ce quiz
    $hasParticipated = count($userReponses) > 0;

    // Afficher les résultats
    return $this->render('quiz/results.html.twig', [
        'quiz' => $quiz,
        'userReponses' => $userReponses,
        'score' => $score,
        'totalQuestions' => $totalQuestions,
        'attempts' => $attempts, // Passer le nombre de tentatives au template
        'hasParticipated' => $hasParticipated, // Passer l'état de participation au template
    ]);
}



#[Route('/generate-questions', name: 'app_quiz_generate_questions', methods: ['GET'])]
public function generateQuestions(Request $request, QuestionRepository $questionRepository): JsonResponse
{
    $niveau = $request->query->get('niveau', 'facile');
    $nomCoursId = $request->query->get('nomCours');
    $nombreQuestions = $request->query->get('nombreQuestions', 4); // Récupérer le nombre de questions demandé

    // Récupérer les questions en fonction du niveau et du cours
    $questions = $questionRepository->findByNiveauAndNomCourscours($niveau, $nomCoursId);

    // Sélectionner un nombre aléatoire de questions
    shuffle($questions);
    $selectedQuestions = array_slice($questions, 0, $nombreQuestions); // Utiliser le nombre de questions demandé

    // Formater les questions pour la réponse JSON
    $formattedQuestions = array_map(function ($question) {
        return [
            'id' => $question->getId(),
            'texte' => $question->getTexte(),
        ];
    }, $selectedQuestions);

    return $this->json($formattedQuestions);
}




#[Route('/stats/global', name: 'quiz_global_stats', methods: ['GET'])]
public function globalStats(EntityManagerInterface $entityManager): Response
{
    // Récupérer tous les résultats de quiz
    $quizResults = $entityManager->getRepository(QuizResult::class)->findAll();

    // Préparer les données pour les scores
    $labels = []; // Noms des utilisateurs
    $scores = []; // Scores des utilisateurs
    $totalScores = 0; // Somme totale des scores
    $maxScore = 0; // Score maximal
    $minScore = PHP_INT_MAX; // Score minimal

    // Préparer les données pour les quiz passés par cours
    $coursePassedCounts = []; // Nombre de quiz passés par cours
    $courseTotalCounts = []; // Nombre total de quiz par cours

    // Préparer les données pour le pie chart (quiz passés vs non passés)
    $passedCount = 0; // Nombre de quiz passés
    $notPassedCount = 0; // Nombre de quiz non passés

    foreach ($quizResults as $result) {
        $user = $result->getUser();
        $username = $user->getEmail(); // Utiliser l'email comme identifiant (ou une autre propriété)

        // Traitement des scores
        $score = $result->getScore();
        $labels[] = $username;
        $scores[] = $score;

        $totalScores += $score;
        if ($score > $maxScore) {
            $maxScore = $score;
        }
        if ($score < $minScore) {
            $minScore = $score;
        }

        // Traitement des quiz passés par cours
        $quiz = $result->getQuiz();
        $course = $quiz->getNomCours();
        $courseName = $course ? $course->getNom() : 'Unknown Course';

        if (!isset($coursePassedCounts[$courseName])) {
            $coursePassedCounts[$courseName] = 0;
            $courseTotalCounts[$courseName] = 0;
        }

        $courseTotalCounts[$courseName]++;
        if ($result->isPassed()) {
            $coursePassedCounts[$courseName]++;
        }

        // Traitement des quiz passés vs non passés
        if ($result->isPassed()) {
            $passedCount++;
        } else {
            $notPassedCount++;
        }
    }

    // Calculer le pourcentage de quiz réussis par cours
    $courseStats = [];
    foreach ($coursePassedCounts as $courseName => $passedCount) {
        $totalCount = $courseTotalCounts[$courseName];
        $percentage = $totalCount > 0 ? ($passedCount / $totalCount) * 100 : 0;
        $courseStats[] = [
            'courseName' => $courseName,
            'passedPercentage' => $percentage,
        ];
    }

    // Calculer la moyenne des scores
    $averageScore = count($scores) > 0 ? $totalScores / count($scores) : 0;

    // Passer les statistiques et les graphiques au template
    return $this->render('quiz/global_stats.html.twig', [
        'labels' => $labels,
        'scores' => $scores,
        'averageScore' => $averageScore,
        'maxScore' => $maxScore,
        'minScore' => $minScore,
        'totalResults' => count($scores),
        'courseStats' => $courseStats,
        'passedCount' => $passedCount, // Nombre de quiz passés
        'notPassedCount' => $notPassedCount, // Nombre de quiz non passés
    ]);
}


/*#[Route('/stats/global', name: 'quiz_global_stats', methods: ['GET'])]
public function globalStats(EntityManagerInterface $entityManager): Response
{
    // Récupérer tous les résultats de quiz
    $quizResults = $entityManager->getRepository(QuizResult::class)->findAll();

    // Préparer les données pour le graphique
    $labels = []; // Noms des utilisateurs
    $passedCounts = []; // Nombre de quiz passés par utilisateur
    $totalPassed = 0; // Nombre total de quiz passés

    // Tableau temporaire pour stocker le nombre de quiz passés par utilisateur
    $userPassedCounts = [];

    foreach ($quizResults as $result) {
        $user = $result->getUser();
        $username = $user->getEmail(); // Utiliser l'email comme identifiant (ou une autre propriété)

        // Si l'utilisateur n'est pas encore dans le tableau, l'ajouter
        if (!isset($userPassedCounts[$username])) {
            $userPassedCounts[$username] = 0;
        }

        // Si le quiz est passé, incrémenter le compteur
        if ($result->isPassed()) {
            $userPassedCounts[$username]++;
            $totalPassed++;
        }
    }

    // Préparer les données pour le graphique
    foreach ($userPassedCounts as $username => $count) {
        $labels[] = $username;
        $passedCounts[] = $count;
    }

    // Passer les statistiques et le graphique au template
    return $this->render('quiz/global_stats.html.twig', [
        'labels' => $labels,
        'passedCounts' => $passedCounts,
        'totalPassed' => $totalPassed,
    ]);
}*/

#[Route('/quiz/{id}/results/pdf', name: 'quiz_results_pdf', methods: ['GET'])]
public function generatePdf(Quiz $quiz, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    // Vérifier que l'utilisateur est connecté
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
    }

    // Récupérer les réponses de l'utilisateur pour ce quiz
    $userReponses = $entityManager->getRepository(UserReponse::class)->findBy([
        'quiz' => $quiz,
        'user' => $user,
    ]);

    // Calculer le score
    $score = 0;
    foreach ($userReponses as $userReponse) {
        if ($userReponse->isEstCorrecte()) {
            $score++;
        }
    }

    // Calculer le nombre total de questions
    $totalQuestions = count($quiz->getQuestions());

    // Déterminer si l'utilisateur a réussi ou échoué
    $passingThreshold = $totalQuestions / 2; // Par exemple, réussir si l'utilisateur obtient au moins 50%
    $passed = $score >= $passingThreshold;

    // Configurer Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);

    // Générer le HTML pour le PDF
    $html = $this->renderView('quiz/results_pdf.html.twig', [
        'quiz' => $quiz,
        'userReponses' => $userReponses,
        'score' => $score,
        'totalQuestions' => $totalQuestions,
        'passed' => $passed, // Passer l'état de réussite au template
    ]);

    // Charger le HTML dans Dompdf
    $dompdf->loadHtml($html);

    // (Optionnel) Configurer le format et l'orientation du PDF
    $dompdf->setPaper('A4', 'portrait');

    // Rendre le PDF
    $dompdf->render();

    // Générer le nom du fichier PDF
    $filename = sprintf('quiz_results_%d.pdf', $quiz->getId());

    // Envoyer le PDF au navigateur pour téléchargement
    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
    ]);
}






#[Route('/quiz/{id}/statistics', name: 'quiz_single_statistics', methods: ['GET'])]
public function quizSingleStatistics(int $id, EntityManagerInterface $entityManager): Response
{
    // Récupérer le quiz par son ID
    $quiz = $entityManager->getRepository(Quiz::class)->find($id);

    if (!$quiz) {
        throw $this->createNotFoundException('Quiz non trouvé.');
    }

    // Récupérer les résultats pour ce quiz
    $quizResults = $entityManager->getRepository(QuizResult::class)->findBy(['quiz' => $quiz]);

    // Compter les quiz passés et non passés
    $passedCount = 0;
    $notPassedCount = 0;
    foreach ($quizResults as $result) {
        if ($result->isPassed()) {
            $passedCount++;
        } else {
            $notPassedCount++;
        }
    }

    // Passer les données au template
    return $this->render('quiz/single_quiz_statistics.html.twig', [
        'quiz' => $quiz,
        'passedCount' => $passedCount,
        'notPassedCount' => $notPassedCount,
    ]);
}









}
<?php

namespace App\Controller;

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
    
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizzes,
            'nomCours' => $nomCours,
            'selectedCours' => $coursId, // Conserver la sélection du filtre de cours
            'selectedNiveau' => $niveau, // Conserver la sélection du filtre de niveau
        ]);
    }


    
    #[Route('/quiz/list', name: 'app_quiz_list', methods: ['GET'])]
    public function list(
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
    
        return $this->render('quiz/list.html.twig', [
            'quizzes' => $quizzes,
            'nomCours' => $nomCours,
            'selectedCours' => $coursId, // Conserver la sélection du filtre de cours
            'selectedNiveau' => $niveau, // Conserver la sélection du filtre de niveau
        ]);
    }



    #[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz); // Use QuizType for new action
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission for new quiz
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
public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
{
    // Create the form with the EditQuizType
    $form = $this->createForm(EditQuizType::class, $quiz);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Only the 'nom' field will be updated
        $entityManager->flush();

        $this->addFlash('success', 'Le nom du quiz a été mis à jour avec succès.');
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
        // Step 1: Delete related user_reponse records using DQL
        $quizId = $quiz->getId();
        $query = $entityManager->createQuery(
            'DELETE FROM App\Entity\UserReponse ur WHERE ur.quiz = :quizId'
        )->setParameter('quizId', $quizId);
        $query->execute();

        // Step 2: Delete the Quiz
        $entityManager->remove($quiz);
        $entityManager->flush();
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

    // Afficher les résultats
    return $this->render('quiz/results.html.twig', [
        'quiz' => $quiz,
        'userReponses' => $userReponses,
        'score' => $score,
        'totalQuestions' => $totalQuestions,
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

}
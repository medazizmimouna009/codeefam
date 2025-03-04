<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\NomCoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/question')]
class QuestionController extends AbstractController
{
    #[Route('/', name: 'app_question_index', methods: ['GET'])]
    public function index(
        Request $request,
        QuestionRepository $questionRepository,
        NomCoursRepository $nomCoursRepository,
        PaginatorInterface $paginator // Ajouter le PaginatorInterface
    ): Response {
        // Récupérer les paramètres de filtrage
        $niveau = $request->query->get('niveau');
        $nomCoursId = $request->query->get('nomCours');
    
        // Convertir $nomCoursId en entier (ou null si vide)
        $nomCoursId = $nomCoursId !== null && $nomCoursId !== '' ? (int)$nomCoursId : null;
    
        // Récupérer les questions filtrées en utilisant la méthode findByNiveauAndNomCours
        $query = $questionRepository->findByNiveauAndNomCours($niveau, $nomCoursId);
    
        // Paginer les résultats
        $questions = $paginator->paginate(
            $query, // Requête filtrée
            $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
            3 // Nombre d'éléments par page (ajustez selon vos besoins)
        );
    
        // Récupérer tous les cours pour le filtre
        $nomCours = $nomCoursRepository->findAll();
    
        return $this->render('question/index.html.twig', [
            'questions' => $questions,
            'nomCours' => $nomCours,
            'selectedNiveau' => $niveau, // Conserver la sélection du filtre de niveau
            'selectedCours' => $nomCoursId, // Conserver la sélection du filtre de cours
        ]);
    }

    #[Route('/new', name: 'app_question_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $question = new Question();
    $form = $this->createForm(QuestionType::class, $question);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_question_index');
    }

    return $this->render('question/new.html.twig', [
        'question' => $question,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_question_show', methods: ['GET'])]
    public function show(Question $question): Response
    {
        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_question_index');
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_question_index');
    }
    #[Route('/questions/by-niveau', name: 'app_questions_by_niveau', methods: ['GET'])]
    public function getQuestionsByNiveau(Request $request, QuestionRepository $questionRepository): JsonResponse
    {
        $niveau = $request->query->get('niveau', 'facile'); // Récupérer le niveau depuis la requête
        $questions = $questionRepository->findBy(['niveau' => $niveau]);
    
        // Formater les questions pour la réponse JSON
        $formattedQuestions = array_map(function ($question) {
            return [
                'id' => $question->getId(),
                'texte' => $question->getTexte(),
            ];
        }, $questions);
    
        return $this->json($formattedQuestions);
    }}
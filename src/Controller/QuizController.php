<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\QuizType;
use App\Entity\NomCours;
use App\Entity\Question;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use App\Repository\NomCoursRepository;
use App\Repository\ReponseRepository;
use App\Entity\UserReponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

#[Route('/quiz')]
final class QuizController extends AbstractController
{
#[Route(name: 'app_quiz_index', methods: ['GET'])]
public function index(QuizRepository $quizRepository, NomCoursRepository $nomCoursRepository, Request $request): Response
{
$search = $request->query->get('search');
$coursId = $request->query->get('cours');

// Cast $coursId to an integer if it's not null
$coursId = $coursId !== null ? (int)$coursId : null;

$quizzes = $quizRepository->findBySearchAndCours($search, $coursId);
$cours = $nomCoursRepository->findAll();


return $this->render('quiz/index.html.twig', ['quizzes' => $quizzes,'cours' => $cours,]);
}



#[Route('/quizzes', name: 'app_quiz_list', methods: ['GET'])]
public function list(QuizRepository $quizRepository): Response
{
    // Fetch all quizzes
    $quizzes = $quizRepository->findAll();

    // Pass the quizzes to the template
    return $this->render('quiz/list.html.twig', [
        'quizzes' => $quizzes,
    ]);
}






#[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, QuestionRepository $questionRepository): Response
{
    $quiz = new Quiz();
    $form = $this->createForm(QuizType::class, $quiz);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Get the generated question from the request
        $generatedQuestionText = $request->request->get('generated_question');

        if ($generatedQuestionText) {
            // Create a new Question entity
            $question = new Question();
            $question->setTexte($generatedQuestionText);
            $question->setNomCours($quiz->getNomCours());

            // Add the question to the quiz
            $quiz->addQuestion($question);

            // Persist the question
            $entityManager->persist($question);
        }

        // Save the quiz
        $entityManager->persist($quiz);
        $entityManager->flush();

        return $this->redirectToRoute('app_quiz_index');
    }

    return $this->render('quiz/new.html.twig', [
        'form' => $form->createView(),
    ]);
}




#[Route('/randomize-questions', name: 'app_quiz_randomize_questions', methods: ['POST'])]
public function randomizeQuestions(Request $request, QuestionRepository $questionRepository): Response
{
    $coursId = $request->request->get('cours_id');
    $questions = $questionRepository->findRandomQuestionsByCours($coursId, 1);

    $questionsArray = array_map(function ($question) {
        return [
            'id' => $question->getId(),
            'texte' => $question->getTexte(),
        ];
    }, $questions);

    return $this->json(['questions' => $questionsArray]);
}


    
#[Route('/{id}', name: 'app_quiz_show', methods: ['GET'])]
public function show(Quiz $quiz): Response
{
return $this->render('quiz/show.html.twig', [
'quiz' => $quiz,
]);
}

#[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
{
$form = $this->createForm(QuizType::class, $quiz);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$entityManager->flush();

return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
}

return $this->render('quiz/edit.html.twig', [
'quiz' => $quiz,
'form' => $form,
]);
}

#[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
{
if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
$entityManager->remove($quiz);
$entityManager->flush();
}

return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
}








#[Route('/{id}/take', name: 'app_quiz_take', methods: ['GET', 'POST'])]
public function takeQuiz(
    int $id,
    Request $request,
    QuizRepository $quizRepository,
    QuestionRepository $questionRepository
): Response {
    $quiz = $quizRepository->find($id);
    if (!$quiz) {
        throw $this->createNotFoundException('Quiz not found.');
    }

    $questionsWithReponses = $questionRepository->findQuestionsWithReponsesByQuizId($id);

    $questionsData = [];
    foreach ($questionsWithReponses as $row) {
        if (!isset($questionsData[$row['question_id']])) {
            $questionsData[$row['question_id']] = [
                'id' => $row['question_id'],
                'texte' => $row['question_texte'],
                'reponses' => [],
            ];
        }
        if ($row['reponse_id']) {
            $questionsData[$row['question_id']]['reponses'][] = [
                'id' => $row['reponse_id'],
                'texte' => $row['reponse_texte'],
                'est_correcte' => (bool) $row['reponse_est_correcte'],
            ];
        }
    }

    dump($questionsData); // Vérifiez le contenu de $questionsData

    return $this->render('quiz/take.html.twig', [
        'quiz' => $quiz,
        'questions' => array_values($questionsData),
    ]);
}

}
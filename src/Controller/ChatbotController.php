<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GeminiService;

class ChatbotController extends AbstractController
{
    private GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    #[Route('/chatbot', name: 'chatbot', methods: ['GET', 'POST'])]
    public function chatbot(Request $request): JsonResponse
    {
        if ($request->isMethod('GET')) {
            return new JsonResponse(['message' => 'Use a POST request to talk to the chatbot.']);
        }

        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        if (!$userMessage) {
            return new JsonResponse(['error' => 'Message is empty'], 400);
        }

        $response = $this->geminiService->getGeminiResponse($userMessage);

        return new JsonResponse(['response' => $response]);
    }
}

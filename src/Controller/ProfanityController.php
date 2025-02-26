<?php

namespace App\Controller;

use App\Service\ProfanityFilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfanityController extends AbstractController
{
    #[Route('/check-profanity', name: 'check_profanity', methods: ['POST'])]
    public function checkProfanity(Request $request, ProfanityFilterService $profanityFilterService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        $censoredText = $profanityFilterService->censorText($text);

        return $this->json([
            'original' => $text,
            'censored' => $censoredText,
        ]);
    }
}

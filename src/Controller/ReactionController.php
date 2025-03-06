<?php

// src/Controller/ReactionController.php
namespace App\Controller;

use App\Entity\Reaction;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ReactionController extends AbstractController
{
    #[Route('/reaction', name: 'add_reaction', methods: ['POST'])]
    public function react(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $emoji = $data['emoji'] ?? null;
        $postId = $data['postId'] ?? null;

        if (!$user || !$emoji || !$postId) {
            return new JsonResponse(['message' => 'Invalid request'], 400);
        }

        $post = $entityManager->getRepository(Post::class)->find($postId);
        if (!$post) {
            return new JsonResponse(['message' => 'Post not found'], 404);
        }

        $reaction = $entityManager->getRepository(Reaction::class)
            ->findOneBy(['user' => $user, 'post' => $post]);

        if ($reaction) {
            if ($reaction->getEmoji() === $emoji) {
                $entityManager->remove($reaction);
            } else {
                $reaction->setEmoji($emoji);
                $entityManager->persist($reaction);
            }
        } else {
            $reaction = new Reaction();
            $reaction->setUser($user);
            $reaction->setEmoji($emoji);
            $reaction->setPost($post);
            $entityManager->persist($reaction);
        }

        $entityManager->flush();
        return new JsonResponse(['message' => 'Reaction saved']);
    }
}


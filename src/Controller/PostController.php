<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Entity\Reaction;
use App\Repository\ReactionRepository;
use App\Service\GeminiService;
use App\Service\ProfanityFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    private GeminiService $geminiService;
    private ProfanityFilterService $profanityFilterService;

    public function __construct(GeminiService $geminiService, ProfanityFilterService $profanityFilterService)
    {
        $this->geminiService = $geminiService;
        $this->profanityFilterService = $profanityFilterService;
    }

    #[Route(name: 'app_post_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PostRepository $postRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user_id = $user instanceof User ? $user->getId() : null;

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user instanceof User) {
                $post->setIdUser($user);
            }

            // Censor the content
            $censoredContent = $this->profanityFilterService->censorText($post->getContenu());
            $post->setContenu($censoredContent);

            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $post->setImage($newFilename);
            }

            // Set the date_creation field
            $post->setDateCreation(new \DateTime());

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        $userMessage = $request->get('message', '');
        $responseMessage = '';

        if ($userMessage) {
            // Get response from Gemini
            try {
                $responseMessage = $this->geminiService->getGeminiResponse($userMessage);
            } catch (\Exception $e) {
                $responseMessage = 'Sorry, the service is currently unavailable. Please try again later.';
            }
        }

        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'form' => $form->createView(),
            'user_id' => $user_id,
            'responseMessage' => $responseMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user_id = $user instanceof User ? $user->getId() : null;
        if ($post->getIdUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit this post.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Censor the content
            $censoredContent = $this->profanityFilterService->censorText($post->getContenu());
            $post->setContenu($censoredContent);

            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $post->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'form' => $form->createView(),
            'user_id' => $user_id,
            'edit_post' => $post,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, PostRepository $postRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $post = $postRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $user = $this->getUser();
        if ($post->getIdUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You cannot delete this post.');
        }

        $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->isCsrfTokenValid('delete'.$post->getId(), $csrfToken)) {
            return new Response('Invalid CSRF token', 400);
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return new Response(null, 204);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function show(Request $request, PostRepository $postRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $post = $postRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        // Censor the content before displaying
        $censoredContent = $this->profanityFilterService->censorText($post->getContenu());
        $post->setContenu($censoredContent);

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof User) {
                $commentaire->setUser($user);
            }
            $commentaire->setPost($post);

            // Censor the comment content
            $censoredCommentContent = $this->profanityFilterService->censorText($commentaire->getContenu());
            $commentaire->setContenu($censoredCommentContent);

            // Set the date_creation field
            $commentaire->setDateCreation(new \DateTime());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        $editForms = [];
        foreach ($post->getCommentaires() as $comment) {
            $editForms[$comment->getId()] = $this->createForm(CommentaireType::class, $comment)->createView();
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'edit_forms' => $editForms,
        ]);
    }

    #[Route('/commentaire/{id}/edit', name: 'app_commentaire_edit', methods: ['POST'])]
    public function editComment(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($commentaire->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit this comment.');
        }

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Censor the comment content
            $censoredCommentContent = $this->profanityFilterService->censorText($commentaire->getContenu());
            $commentaire->setContenu($censoredCommentContent);

            $entityManager->flush();

            return $this->redirectToRoute('app_post_show', ['id' => $commentaire->getPost()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_post_show', ['id' => $commentaire->getPost()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin', name: 'app_post_admin_index', methods: ['GET'])]
    public function adminIndex(PostRepository $postRepository): Response
    {
        return $this->render('post/admin_index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/{id}/react', name: 'app_post_react', methods: ['POST'])]
    public function react(Request $request, Post $post, ReactionRepository $reactionRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $emoji = $data['emoji'] ?? null;

        if (!$emoji) {
            return $this->json(['success' => false, 'message' => 'Invalid emoji'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $reaction = new Reaction();
        $reaction->setEmoji($emoji);
        $reaction->setPost($post);
        $reaction->setUser($user);

        $entityManager->persist($reaction);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}

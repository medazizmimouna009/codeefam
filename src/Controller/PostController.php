<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PostRepository $postRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user_id = $user instanceof User ? $user->getId() : null; // Assuming you have a method to get the current user ID

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user instanceof User) {
                $post->setIdUser($user);
            }

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

        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'form' => $form->createView(),
            'user_id' => $user_id, // Pass the user_id to the template
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user_id = $user instanceof User ? $user->getId() : null; // Assuming you have a method to get the current user ID
        if ($post->getIdUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit this post.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            'user_id' => $user_id, // Pass the user_id to the template
        ]);
    }

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($post->getIdUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot delete this post.');
        }

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}

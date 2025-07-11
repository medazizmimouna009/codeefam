<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/commentaire')]
final class CommentaireController extends AbstractController
{
    #[Route(name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof User) {
                $commentaire->setUser($user);
            }

            // Set the date_creation field
            $commentaire->setDateCreation(new \DateTime());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_new', [], Response::HTTP_SEE_OTHER);
        }

        $commentaires = $commentaireRepository->findAll();
        $editForms = [];
        foreach ($commentaires as $comment) {
            $editForms[$comment->getId()] = $this->createForm(CommentaireType::class, $comment)->createView();
        }

        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
            'commentaires' => $commentaires,
            'edit_forms' => $editForms,
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($commentaire->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit this comment.');
        }

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_commentaire_new', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_commentaire_delete', methods: ['POST'])]
public function delete(Request $request, EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, int $id): Response
{
    $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);
    
    if (!$commentaire) {
        return $this->json(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
    }

    $user = $this->getUser();
    
    if ($commentaire->getUser() !== $user && !$authChecker->isGranted('ROLE_ADMIN')) {
        return $this->json(['message' => 'Access Denied'], Response::HTTP_FORBIDDEN);
    }

    if (!$this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
        return $this->json(['message' => 'Invalid CSRF token'], Response::HTTP_BAD_REQUEST);
    }

    $entityManager->remove($commentaire);
    $entityManager->flush();

    return $this->json(['message' => 'Comment deleted'], Response::HTTP_NO_CONTENT);
}

}
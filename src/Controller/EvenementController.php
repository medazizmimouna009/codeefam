<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Service\GoogleCalendarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'evenement_afficher', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        $evenements = $evenementRepository->findAll();

        return $this->render('evenement/afficherEvenement.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/new', name: 'evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('evenement_afficher');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Événement modifié avec succès !');
            return $this->redirectToRoute('evenement_afficher');
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'evenement_delete', methods: ['POST'])]
public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): JsonResponse
{
    if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
        $entityManager->remove($evenement);
        $entityManager->flush();
        return new JsonResponse(['success' => true, 'message' => 'Événement supprimé avec succès !']);
    }
    return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression.'], 400);
}

    #[Route('/json', name: 'evenement_json', methods: ['GET'])]
    public function getEventsJson(EvenementRepository $evenementRepository): JsonResponse
    {
        $evenements = $evenementRepository->findAll();
        $events = [];

        foreach ($evenements as $evenement) {
            $events[] = [
                'id' => $evenement->getId(),
                'title' => $evenement->getTitre(),
                'start' => $evenement->getStartDate()->format('c'),
                'end' => $evenement->getEndDate() ? $evenement->getEndDate()->format('c') : null,
                'location' => $evenement->getLocation(),
                'description' => $evenement->getDescription(),
                'type' => $evenement->getType(),
            ];
        }

        return new JsonResponse($events);
    }

    #[Route('/google-auth', name: 'google_auth', methods: ['GET'])]
    public function googleAuth(GoogleCalendarService $googleCalendarService): Response
    {
        return new RedirectResponse($googleCalendarService->getAuthUrl());
    }

    #[Route('/google-callback', name: 'google_callback', methods: ['GET'])]
    public function googleCallback(Request $request, GoogleCalendarService $googleCalendarService): Response
    {
        $code = $request->query->get('code');
        if ($code) {
            try {
                $googleCalendarService->authenticate($code);
                $this->addFlash('success', 'Authentification réussie avec Google Calendar.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l’authentification : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Code d’authentification manquant.');
        }

        return $this->redirectToRoute('evenement_afficher');
    }

    #[Route('/{id}/sync-google', name: 'evenement_sync_google', methods: ['GET'])]
    public function syncToGoogle(int $id, EvenementRepository $evenementRepository, GoogleCalendarService $googleCalendarService, Request $request): Response
    {
        $evenement = $evenementRepository->find($id);
        if (!$evenement) {
            $this->addFlash('error', 'Événement introuvable.');
            return $this->redirectToRoute('evenement_afficher');
        }

        $session = $request->getSession();
        $accessToken = $session->get('google_access_token');
        if (!$accessToken) {
            $this->addFlash('info', 'Veuillez vous authentifier avec Google pour synchroniser.');
            return $this->redirectToRoute('google_auth');
        }

        try {
            $eventLink = $googleCalendarService->createEvent([
                'title' => $evenement->getTitre(),
                'location' => $evenement->getLocation(),
                'description' => $evenement->getDescription(),
                'start' => $evenement->getStartDate(),
                'end' => $evenement->getEndDate(),
            ]);
            $this->addFlash('success', 'Événement synchronisé avec Google Calendar ! <a href="' . $eventLink . '" target="_blank">Voir sur Google Calendar</a>');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la synchronisation : ' . $e->getMessage());
        }

        return $this->redirectToRoute('evenement_afficher');
    }

    
}
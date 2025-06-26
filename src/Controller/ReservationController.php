<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'reservation_afficher', methods: ['GET'])]
    public function afficherReservations(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();
        return $this->render('reservation/afficherReservation.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservation/new', name: 'reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($reservation);
                $entityManager->flush();
                $this->addFlash('success', 'Réservation ajoutée avec succès !');
                return $this->redirectToRoute('app_home');
                        } else {
                $this->addFlash('error', 'Erreur lors de la création de la réservation. Vérifiez les champs.');
                dump($form->getErrors(true));
            }
        }
    
        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenement/calendar', name: 'evenement_calendar')]
    public function calendar(): Response
    {
        return $this->render('evenement/calendar.html.twig');
    }
    
    #[Route('/reservation/{id}/edit', name: 'reservation_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirectToRoute('reservation_afficher');
        }
    
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Réservation modifiée avec succès.');
                return $this->redirectToRoute('reservation_afficher');
            } else {
                $this->addFlash('error', 'Erreur lors de la modification. Vérifiez les champs du formulaire.');
                dump($form->getErrors(true));
            }
        }
    
        return $this->render('reservation/edit.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservation/{id}/delete', name: 'reservation_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable.');
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Réservation introuvable.'], 404);
            }
            return $this->redirectToRoute('reservation_afficher');
        }

        try {
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('success', 'Réservation supprimée avec succès.');

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Réservation supprimée avec succès.']);
            }

            return $this->render('reservation/_delete_success.html.twig', [
                'id' => $id,
            ], new Response('', 200, ['Content-Type' => 'text/vnd.turbo-stream.html']));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la réservation.');
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression.'], 500);
            }
            return $this->redirectToRoute('reservation_afficher');
        }
    }
}
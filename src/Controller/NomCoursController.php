<?php

namespace App\Controller;

use App\Entity\NomCours;
use App\Form\NomCoursType;
use App\Repository\CategorieRepository;
use App\Repository\NomCoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nom/cours')]
final class NomCoursController extends AbstractController
{
    #[Route(name: 'app_nom_cours_index', methods: ['GET'])]
    public function index(NomCoursRepository $nomCoursRepository, CategorieRepository $categorieRepository): Response
    {
        return $this->render('nom_cours/index.html.twig', [
            'nom_cours' => $nomCoursRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
        ]);
    }
    #[Route('/nom/cours/by-categorie', name: 'app_nom_cours_by_categorie', methods: ['GET'])]
public function getNomCoursByCategorie(Request $request, NomCoursRepository $nomCoursRepository): Response
{
    $categorieId = $request->query->get('categorieId');
    $nomCours = $nomCoursRepository->findBy(['categorie' => $categorieId]);

    $response = [];
    foreach ($nomCours as $nomCour) {
        $response[] = [
            'id' => $nomCour->getId(),
            'nom' => $nomCour->getNom(),
        ];
    }

    return $this->json($response);
}

    #[Route('/new', name: 'app_nom_cours_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $nomCour = new NomCours();
    $form = $this->createForm(NomCoursType::class, $nomCour);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($nomCour);
        $entityManager->flush();

        $this->addFlash('success', 'Le nom du cours a été créé avec succès.');
        return $this->redirectToRoute('app_nom_cours_index', [], Response::HTTP_SEE_OTHER);
    }

    // Afficher les erreurs de validation
    if ($form->isSubmitted() && !$form->isValid()) {
        dump($form->getErrors(true)); // Affiche toutes les erreurs de validation
    }

    return $this->render('nom_cours/new.html.twig', [
        'nom_cour' => $nomCour,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_nom_cours_show', methods: ['GET'])]
    public function show(NomCours $nomCour): Response
    {
        return $this->render('nom_cours/show.html.twig', [
            'nom_cour' => $nomCour,
        ]);
    }

    

    #[Route('/{id}/edit', name: 'app_nom_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NomCours $nomCour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NomCoursType::class, $nomCour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nom_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nom_cours/edit.html.twig', [
            'nom_cour' => $nomCour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nom_cours_delete', methods: ['POST'])]
    public function delete(Request $request, NomCours $nomCour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nomCour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($nomCour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nom_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}

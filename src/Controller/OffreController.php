<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Entity\Achat;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    #[Route(name: 'app_offre_index', methods: ['GET'])]
    public function index(Request $request, OffreRepository $offreRepository): Response
    {
        $prix = $request->query->get('prix');

        // Recherche des offres si un titre est fourni
        if ($prix) {
            $offres = $offreRepository->findByPrix($prix);  // Appeler la méthode findByPrix
        } else {
            // Sinon, afficher toutes les offres
            $offres = $offreRepository->findAll();
        }

        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
            'prix' => $prix,  // Transmettre le titre à la vue pour le remplir dans la barre de recherche
        ]);
    }

    

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }







    #[Route('/{id}/acheter', name: 'app_achat', methods: ['POST'])]
    public function participate(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
       
        $user = $this->getUser();
   
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour acheter un offre.');
            return $this->redirectToRoute('app_login');
        }
   
       
        $existingAchat = $entityManager->getRepository(Achat::class)->findOneBy([
            'utilisateur' => $user,
            'offre' => $offre
        ]);
   
        if ($existingAchat) {
            $this->addFlash('info', 'Vous avez déjà acheté cet offre.');
            return $this->redirectToRoute('app_offre_index');
        }
   
        $achat = new Achat();
        $achat->setUtilisateur($user);
        $achat->setOffre($offre);
        $achat->setDateAchat(new \DateTime());
        $achat->setTypeAchat('offre');
        $entityManager->persist($achat);
        $entityManager->flush();
   
        $this->addFlash('success', 'Vous avez acheté cet offre avec succée !');
        return $this->redirectToRoute('app_dashboard ');
    }

}



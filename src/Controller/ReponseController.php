<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\UserReponse;

#[Route('/reponse')]
final class ReponseController extends AbstractController{
    #[Route(name: 'app_reponse_index', methods: ['GET'])]
    public function index(
        ReponseRepository $reponseRepository,
        Request $request,
        PaginatorInterface $paginator // Injectez le PaginatorInterface
    ): Response {
        // Récupérer toutes les réponses avec les questions associées
        $query = $reponseRepository->findAllWithQuestions();

        // Paginer les résultats
        $reponses = $paginator->paginate(
            $query, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
            3 // Nombre d'éléments par page (ajustez selon vos besoins)
        );

        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses, // Passer les réponses paginées au template
        ]);
    }








    
    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Si le formulaire est valide, enregistrez les modifications
                $entityManager->flush();
    
                $this->addFlash('success', 'La réponse a été modifiée avec succès.');
                return $this->redirectToRoute('app_reponse_index');
            } else {
                // Si le formulaire n'est pas valide, affichez les erreurs
                $this->addFlash('error', 'Une erreur s\'est produite lors de la modification de la réponse.');
            }
        }
    
        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/reponse/{id}/delete', name: 'app_reponse_delete', methods: ['POST'])]
    public function deleteReponse(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF pour la sécurité
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            // Supprimer toutes les UserReponse qui référencent cette réponse
            $userReponses = $entityManager->getRepository(UserReponse::class)->findBy(['reponse' => $reponse]);
            foreach ($userReponses as $userReponse) {
                $entityManager->remove($userReponse);
            }
    
            // Supprimer la réponse
            $entityManager->remove($reponse);
            $entityManager->flush();
    
            $this->addFlash('success', 'La réponse a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
    
        return $this->redirectToRoute('app_reponse_index');
    }
}

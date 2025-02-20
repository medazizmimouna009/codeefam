<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;



#[Route('/cours')]
final class CoursController extends AbstractController
{
    #[Route(name: 'app_cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository): Response
    {
        return $this->render('cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
         // Récupérer l'utilisateur actuel (tuteur)
        $tuteur = $this->getUser();
        // Créer un nouveau cours
        $cour = new Cours();
        
        $form = $this->createForm(CoursType::class, $cour
        , [
            'tuteur' => $tuteur, // Passer le tuteur actuel au formulaire
        ]);
        
        // Traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             // Récupérer le tuteur sélectionné et l'associer au cours
             $tuteur = $form->get('tuteur')->getData();
             $cour->setTuteur($tuteur);

             // Gérer l'upload du fichier
             $fichierFile = $form->get('fichierFile')->getData();
             if ($fichierFile) {
                 $originalFilename = pathinfo($fichierFile->getClientOriginalName(), PATHINFO_FILENAME);
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename . '-' . uniqid() . '.' . $fichierFile->guessExtension();
 
                    // Générer un nom unique pour le fichier
                 //$nomFichier = md5(uniqid()) . '.' . $fichierFile->guessExtension();

                 // Déplacer le fichier dans le répertoire de stockage
                 $fichierFile->move(
                     $this->getParameter('fichiers_directory'), // Définir ce paramètre dans services.yaml
                     $newFilename
                 );
 
                 // Enregistrer le nom du fichier dans l'entité
                 $cour->setFichier($newFilename);

             }



            // Sauvegarder le cours dans la base de données
            $entityManager->persist($cour);
            $entityManager->flush();
            $this->addFlash('success', 'Cours ajouté avec succès !');


            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/new.html.twig', [
           // 'cour' => $cour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function show(Cours $cour): Response
    {
        return $this->render('cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->getPayload()->getString('_token'))) {
            // Supprimer le fichier associé
            $fichier = $cour->getFichier();
            if ($fichier) {
                $filePath = $this->getParameter('fichiers_directory') . '/' . $fichier;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }            
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/download', name: 'app_cours_download', methods: ['GET'])]
    public function download(Cours $cour): Response
    {
        $fichier = $cour->getFichier();
    
        if (!$fichier) {
            throw $this->createNotFoundException('Fichier non trouvé.');
        }
    
        $filePath = $this->getParameter('fichiers_directory') . '/' . $fichier;
    
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier non trouvé sur le serveur.');
        }
    
        return $this->file($filePath);
    }


}

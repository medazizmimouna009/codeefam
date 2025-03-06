<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Rating;
use App\Entity\Categorie;
use App\Form\RatingType;
use App\Controller\CategorieController;
use App\Entity\NomCours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface; // on utilise slugger interface pour generer des noms de fichiers uniques 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; //pour securiser le telechargement 
use Symfony\Component\HttpFoundation\JsonResponse;

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

        // Créer un nouveau cours
        $cour = new Cours();

        $form = $this->createForm(CoursType::class, $cour);

        // Traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le tuteur sélectionné et l'associer au cours
            $tuteur = $form->get('tuteur')->getData();
            $cour->setTuteur($tuteur);

            // Associer chaque vidéo au cours
            foreach ($cour->getVideos() as $video) {
                $video->setCours($cour);
                $entityManager->persist($video);
            }

            // Gérer l'upload du fichier
            $fichierFile = $form->get('fichierFile')->getData();
            if ($fichierFile) {
                $originalFilename = pathinfo($fichierFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fichierFile->guessExtension();

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
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cours-by-category/{id}', name: 'cours_by_category', methods: ['GET'])]
    public function getNomCoursByCategory(Categorie $category, EntityManagerInterface $entityManager): JsonResponse
    {
        $cours = $entityManager->getRepository(Cours::class)->findBy(['categorie' => $category]);

        $data = [];
        foreach ($cours as $c) {
            $data[] = [
                'id' => $c->getId(),
                'nom' => $c->getNomCours() // Assure-toi que cette méthode existe dans `Cours`
            ];
        }
        return new JsonResponse(data: $data);
    }

    #[Route('/cours/get-nom-cours/{id}', name: 'get_nom_cours', methods: ['GET'])]
    public function getNomCours(Categorie $categorie, EntityManagerInterface $entityManager): JsonResponse
    {
        $nomsCours = $entityManager->getRepository(NomCours::class)
            ->findBy(['categorie' => $categorie]);

        $data = [];
        foreach ($nomsCours as $nomCours) {
            $data[] = [
                'id' => $nomCours->getId(),
                'nom' => $nomCours->getNom()
            ];
        }

        return new JsonResponse(data: $data);
    }

    #[Route('/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function show(Cours $cour): Response
    {
        return $this->render('cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }  
    #[Route('/{id}/user', name: 'app_cours_show_user', methods: ['GET', 'POST'])]
    public function show_user(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {    dump($id); // Vérifiez l'ID reçu

        $cour = $entityManager->getRepository(Cours::class)->find($id);
    
        if (!$cour) {
            throw $this->createNotFoundException('Le cours demandé n\'existe pas.');
        }
    
        // Récupérer les vidéos associées au cours
        $videos = $cour->getVideos();
    
        // Créer un formulaire de notation pour chaque vidéo
        $ratingForms = [];
        foreach ($videos as $video) {
            $rating = new Rating();
            $rating->setVideo($video);
            $rating->setUser($this->getUser());
    
            $ratingForms[$video->getId()] = $this->createForm(RatingType::class, $rating, [
                'action' => $this->generateUrl('video_rate', ['id' => $video->getId()]),
            ])->createView();
        }
    
        // Afficher la page
        return $this->render('cours/show_user.html.twig', [
            'cour' => $cour,
            'videos' => $videos,
            'ratingForms' => $ratingForms,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]


    public function edit(
        Request $request,
        Cours $cour,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger // Ajout du SluggerInterface pour gérer les noms de fichiers
    ): Response {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour la date de modification
            $cour->setUpdatedAt(new \DateTimeImmutable());

            // Gérer l'upload du fichier
            $fichierFile = $form->get('fichierFile')->getData();
            if ($fichierFile) {
                // Supprimer l'ancien fichier s'il existe
                $ancienFichier = $cour->getFichier();
                if ($ancienFichier) {
                    $cheminFichier = $this->getParameter('fichiers_directory') . '/' . $ancienFichier;
                    if (file_exists($cheminFichier)) {
                        unlink($cheminFichier); // Supprimer le fichier du serveur
                    }
                }

                // Générer un nouveau nom de fichier
                $originalFilename = pathinfo($fichierFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fichierFile->guessExtension();

                // Déplacer le fichier dans le répertoire de stockage
                $fichierFile->move(
                    $this->getParameter('fichiers_directory'),
                    $newFilename
                );

                // Enregistrer le nouveau nom de fichier dans l'entité
                $cour->setFichier($newFilename);
            }

            // Associer chaque vidéo au cours (au cas où de nouvelles vidéos ont été ajoutées)
            foreach ($cour->getVideos() as $video) {
                if (!$video->getCours()) {
                    $video->setCours($cour);
                    $entityManager->persist($video);
                }
            }

            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Le cours a été mis à jour avec succès !');

            // Rediriger vers la liste des cours
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
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
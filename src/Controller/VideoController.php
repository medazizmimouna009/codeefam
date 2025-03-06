<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\RatingType;
use App\Entity\Rating;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/video')]
final class VideoController extends AbstractController
{
    #[Route(name: 'app_video_index', methods: ['GET'])]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

  

    #[Route('/new', name: 'app_video_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('uploadedFile')->getData();
    
            if ($uploadedFile) {
                // Generate a unique name for the file
                $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
    
                // Move the file to the uploads directory
                $uploadedFile->move(
                    $this->getParameter('videos_directory'), // Define this parameter in services.yaml
                    $newFilename
                );
    
                // Set the videoName property in the entity
                $video->setVideoName($newFilename);
            }
    
            // Save the entity to the database
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success', 'Vidéo créée avec succès !');
    
            return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('video/new.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_video_show', methods: ['GET'])]
    public function show(Video $video, EntityManagerInterface $entityManager, Request $request): Response
    {
        
        // Créer un formulaire de notation
        $rating = new Rating();
        $rating->setVideo($video);
        $rating->setUser($this->getUser());
    
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);
    
        return $this->render('video/show.html.twig', [
            'video' => $video,
            'form' => $form->createView(),  // On passe le formulaire au template
        ]);
    }

    #[Route('/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/rate', name: 'video_rate', methods: ['POST'])]
    public function rate(Video $video, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        // Vérifier si l'utilisateur a déjà noté cette vidéo
        $existingRating = $entityManager->getRepository(Rating::class)->findOneBy([
            'video' => $video,
            'user' => $user,
        ]);
    
        if ($existingRating) {
            // Mettre à jour la note existante
            $existingRating->setValue($request->request->get('value'));
        } else {
            // Créer une nouvelle note
            $rating = new Rating();
            $rating->setVideo($video);
            $rating->setUser($user);
            $rating->setValue($request->request->get('value'));
    
            $entityManager->persist($rating);
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'Votre note a été enregistrée.');
        return $this->redirectToRoute('app_cours_show_user', ['id' => $video->getCours()->getId()]);
    }




}

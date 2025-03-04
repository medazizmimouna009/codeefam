<?php

namespace App\Controller;

use App\Entity\Tuteur;
use App\Form\TuteurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tuteur')]
final class TuteurController extends AbstractController{
    #[Route('/admin/tuteurs', name: 'app_tuteur_index', methods: ['GET'])]
    #[Route('/tuteur/tuteurs', name: 'app_index_tuteur_tuteur_side', methods: ['GET'])]
    #[Route('/user/tuteurs', name: 'app_index_tuteur_user_side', methods: ['GET'])]
    public function tuteurIndex(Security $security, Request $request, UserRepository $userRepository): Response
    {
        $tuteurs = $userRepository->findByRole('ROLE_TUTEUR');
        // Vérification des rôles pour choisir le bon template
        if ($security->isGranted('ROLE_ADMIN')) {
            $template = 'tuteur/index.html.twig';
        } elseif ($security->isGranted('ROLE_TUTEUR')) {
            $template = 'tuteur/indexTuteurTuteurSide.html.twig';
        } elseif ($security->isGranted('ROLE_USER')) {
            $template = 'tuteur/indexTuteurUserSide.html.twig';
        } else {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }
    
        return $this->render($template, [
            'tuteurs' => $tuteurs,
        ]);
        
    }
    

    #[Route('/new', name: 'app_tuteur_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher // Inject the password hasher
    ): Response {
        $tuteur = new Tuteur();
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password before persisting
            $plainPassword = $tuteur->getPassword(); // Assuming the password is set in the form
            $hashedPassword = $passwordHasher->hashPassword($tuteur, $plainPassword);
            $tuteur->setPassword($hashedPassword);
    
            $entityManager->persist($tuteur);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_tuteur_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('tuteur/new.html.twig', [
            'tuteur' => $tuteur,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_tuteur_show', methods: ['GET'])]

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TUTEUR") or is_granted("ROLE_USER")'), message: 'Unauthorized access!')]  
    public function show(Tuteur $tuteur): Response
    {
        return $this->render('tuteur/show.html.twig', [
            'tuteur' => $tuteur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tuteur_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Tuteur $tuteur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tuteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tuteur/edit.html.twig', [
            'tuteur' => $tuteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tuteur_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Tuteur $tuteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tuteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tuteur_index', [], Response::HTTP_SEE_OTHER);
    }
}

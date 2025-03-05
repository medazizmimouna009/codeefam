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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tuteur')]
final class TuteurController extends AbstractController{
    #[Route('/admin/tuteurs', name: 'app_tuteur_index', methods: ['GET'])]
    #[Route('/tuteur/tuteurs', name: 'app_index_tuteur_tuteur_side', methods: ['GET'])]
    #[Route('/user/tuteurs', name: 'app_index_tuteur_user_side', methods: ['GET'])]
    public function tuteurIndex(
        Security $security,
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): Response {
        // Récupérer les tuteurs en fonction du rôle
        $query = $userRepository->findByRole('ROLE_TUTEUR');

        // Paginer les résultats
        $tuteurs = $paginator->paginate(
            $query, // Requête pour récupérer les tuteurs
            $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
            1 // Nombre d'éléments par page (ajustez selon vos besoins)
        );
       
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
    

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_tuteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Create a new tuteur object
        $tuteur = new Tuteur();
    
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            // Update the tuteur object with the form data
            $tuteur->setPrenom($request->request->get('prenom'));
            $tuteur->setNom($request->request->get('nom'));
            $tuteur->setEmail($request->request->get('email'));
            $tuteur->setPassword($request->request->get('password')); // Password will be hashed later
            $tuteur->setNumTel($request->request->get('numTel'));
            $tuteur->setDateDeNaissance(new \DateTime($request->request->get('dateDeNaissance')));
            $tuteur->setAdresse($request->request->get('adresse'));
            $tuteur->setSpecialite($request->request->get('specialite'));
            $tuteur->setBio($request->request->get('bio'));
    
            // Validate the tuteur object
            $errors = $validator->validate($tuteur);
            if (count($errors) > 0) {
                // If there are validation errors, pass them to the template
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
    
                return $this->render('tuteur/new.html.twig', [
                    'errors' => $errorMessages, // Pass errors to the template
                    'formData' => $request->request->all(), // Pass submitted form data back to the form
                ]);
            }
    
            // Hash the password before saving
            $hashedPassword = $passwordHasher->hashPassword($tuteur, $tuteur->getPassword());
            $tuteur->setPassword($hashedPassword);
    
            // Save the new tuteur to the database
            $entityManager->persist($tuteur);
            $entityManager->flush();
    
            // Add a success message
            $this->addFlash('success', 'Tuteur crée avec succées!');
    
            // Redirect to a relevant page (e.g., tuteur list or profile)
            return $this->redirectToRoute('app_tuteur_index');
        }
    
        // If the form is not submitted, render the form without errors
        return $this->render('tuteur/new.html.twig', [
            'errors' => [], // No errors initially
            'formData' => [], // No form data initially
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

    #[IsGranted('ROLE_ADMIN')]      
    #[Route('/{id}/edit', name: 'app_tuteur_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Tuteur $tuteur,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            // Update the tuteur object with the form data
            $tuteur->setPrenom($request->request->get('prenom'));
            $tuteur->setNom($request->request->get('nom'));
            $tuteur->setEmail($request->request->get('email'));
            $tuteur->setNumTel($request->request->get('numTel'));
            $tuteur->setDateDeNaissance(new \DateTime($request->request->get('dateDeNaissance')));
            $tuteur->setAdresse($request->request->get('adresse'));
            $tuteur->setBio($request->request->get('bio'));
    
            // Handle password update (only if a new password is provided)
            $newPassword = $request->request->get('password');
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($tuteur, $newPassword);
                $tuteur->setPassword($hashedPassword);
            }
    
            // Validate the tuteur object
            $errors = $validator->validate($tuteur);
            if (count($errors) > 0) {
                // If there are validation errors, pass them to the template
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
    
                return $this->render('tuteur/edit.html.twig', [
                    'tuteur' => $tuteur,
                    'errors' => $errorMessages, // Pass errors to the template
                    'formData' => $request->request->all(), // Pass submitted form data back to the form
                ]);
            }
    
            // Save changes to the database
            $entityManager->flush();
    
            // Add a success message
            $this->addFlash('success', 'tuteur a été modifié!');
    
            // Redirect to the tuteur list
            return $this->redirectToRoute('app_tuteur_index');
        }
    
        // If the form is not submitted, render the form with the current tuteur data
        return $this->render('tuteur/edit.html.twig', [
            'tuteur' => $tuteur,
            'errors' => [], // No errors initially
            'formData' => [
                'prenom' => $tuteur->getPrenom(),
                'nom' => $tuteur->getNom(),
                'email' => $tuteur->getEmail(),
                'numTel' => $tuteur->getNumTel(),
                'dateDeNaissance' => $tuteur->getDateDeNaissance() ? $tuteur->getDateDeNaissance()->format('Y-m-d') : '',
                'adresse' => $tuteur->getAdresse(),
                'bio' => $tuteur->getBio(),
            ], // Pre-fill form data with current tuteur data
        ]);
    }

    #[Route('/{id}', name: 'app_tuteur_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Tuteur $tuteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tuteur);
            $entityManager->flush();
            $this->addFlash('success', 'un tuteur a été supprimé !');

        }

        return $this->redirectToRoute('app_tuteur_index', [], Response::HTTP_SEE_OTHER);
    }
}
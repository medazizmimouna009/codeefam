<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository; // Import UserRepository
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Add this line
use Knp\Component\Pager\PaginatorInterface; // Import PaginatorInterface
use Symfony\Component\Validator\Validator\ValidatorInterface; // Import ValidatorInterface
use Symfony\Component\ExpressionLanguage\Expression; // Import the Expression class
use Symfony\Component\Security\Http\Attribute\IsGranted; // Correct import for IsGranted
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route(name: 'app_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        UserRepository $userRepository, // Add this parameter
        Request $request, // Add this parameter
        PaginatorInterface $paginator // Add this parameter
    ): Response {
        // Fetch the query for admins (ensure this returns a Query object, not an array)
        $query = $userRepository->findByRole('ROLE_ADMIN');
    
        // Paginate the results
        $admins = $paginator->paginate(
            $query, // Query to paginate
            $request->query->getInt('page', 1), // Current page number (default: 1)
            1 // Number of items per page (adjust as needed)
        );
    
        return $this->render('admin/index.html.twig', [
            'admins' => $admins, // Pass the paginated result to the template
        ]);
    }

    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Create a new Admin object
        $admin = new Admin();
    
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            // Update the admin object with the form data
            $admin->setPrenom($request->request->get('prenom'));
            $admin->setNom($request->request->get('nom'));
            $admin->setEmail($request->request->get('email'));
            $admin->setPassword($request->request->get('password')); // Password will be hashed later
            $admin->setNumTel($request->request->get('numTel'));
            $admin->setDateDeNaissance(new \DateTime($request->request->get('dateDeNaissance')));
            $admin->setAdresse($request->request->get('adresse'));
            $admin->setBio($request->request->get('bio'));
    
            // Validate the admin object
            $errors = $validator->validate($admin);
            if (count($errors) > 0) {
                // If there are validation errors, pass them to the template
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
    
                return $this->render('admin/new.html.twig', [
                    'errors' => $errorMessages, // Pass errors to the template
                    'formData' => $request->request->all(), // Pass submitted form data back to the form
                ]);
            }
    
            // Hash the password before saving
            $hashedPassword = $passwordHasher->hashPassword($admin, $admin->getPassword());
            $admin->setPassword($hashedPassword);
    
            // Save the new admin to the database
            $entityManager->persist($admin);
            $entityManager->flush();
    
            // Add a success message
            $this->addFlash('success', 'Admin crée avec succées!');
    
            // Redirect to a relevant page (e.g., admin list or profile)
            return $this->redirectToRoute('app_admin_index');
        }
    
        // If the form is not submitted, render the form without errors
        return $this->render('admin/new.html.twig', [
            'errors' => [], // No errors initially
            'formData' => [], // No form data initially
        ]);
    }


    #[Route('/{id}', name: 'app_admin_show', methods: ['GET'])]

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TUTEUR") or is_granted("ROLE_USER")'), message: 'Unauthorized access!')]  
    public function show(Admin $admin): Response
    {
        return $this->render('admin/show.html.twig', [
            'admin' => $admin,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]      
    #[Route('/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Admin $admin,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            // Update the admin object with the form data
            $admin->setPrenom($request->request->get('prenom'));
            $admin->setNom($request->request->get('nom'));
            $admin->setEmail($request->request->get('email'));
            $admin->setNumTel($request->request->get('numTel'));
            $admin->setDateDeNaissance(new \DateTime($request->request->get('dateDeNaissance')));
            $admin->setAdresse($request->request->get('adresse'));
            $admin->setBio($request->request->get('bio'));
    
            // Handle password update (only if a new password is provided)
            $newPassword = $request->request->get('password');
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($admin, $newPassword);
                $admin->setPassword($hashedPassword);
            }
    
            // Validate the admin object
            $errors = $validator->validate($admin);
            if (count($errors) > 0) {
                // If there are validation errors, pass them to the template
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
    
                return $this->render('admin/edit.html.twig', [
                    'admin' => $admin,
                    'errors' => $errorMessages, // Pass errors to the template
                    'formData' => $request->request->all(), // Pass submitted form data back to the form
                ]);
            }
    
            // Save changes to the database
            $entityManager->flush();
    
            // Add a success message
            $this->addFlash('success', 'Admin a été modifié!');
    
            // Redirect to the admin list
            return $this->redirectToRoute('app_admin_index');
        }
    
        // If the form is not submitted, render the form with the current admin data
        return $this->render('admin/edit.html.twig', [
            'admin' => $admin,
            'errors' => [], // No errors initially
            'formData' => [
                'prenom' => $admin->getPrenom(),
                'nom' => $admin->getNom(),
                'email' => $admin->getEmail(),
                'numTel' => $admin->getNumTel(),
                'dateDeNaissance' => $admin->getDateDeNaissance() ? $admin->getDateDeNaissance()->format('Y-m-d') : '',
                'adresse' => $admin->getAdresse(),
                'bio' => $admin->getBio(),
            ], // Pre-fill form data with current admin data
        ]);
    }

    #[Route('/{id}', name: 'app_admin_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]  
    public function delete(Request $request, Admin $admin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admin->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($admin);
            $entityManager->flush();
            $this->addFlash('success', 'un utilisateur a été supprimé!');

        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
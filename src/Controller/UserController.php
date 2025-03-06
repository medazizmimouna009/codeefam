<?php

    namespace App\Controller;
    
    use App\Entity\User;
    use App\Form\UserType;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Security\Core\Security;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Component\ExpressionLanguage\Expression;
    use Symfony\Component\Security\Http\Attribute\IsGranted;
    
    #[Route('/user')]
    final class UserController extends AbstractController{
        #[Route('/admin/users', name: 'app_user_index', methods: ['GET'])]
        #[Route('/tuteur/users', name: 'app_index_tuteur_side', methods: ['GET'])]
        #[Route('/user/users', name: 'app_index_user_side', methods: ['GET'])]
        public function index(
            Security $security,
            Request $request,
            UserRepository $userRepository,
            PaginatorInterface $paginator
        ): Response {
            // Fetch the query for users with the role 'ROLE_USER'
            $query = $userRepository->findByRole('ROLE_USER');
        
            // Paginate the results
            $users = $paginator->paginate(
                $query, // Query to paginate
                $request->query->getInt('page', 1), // Current page number (default: 1)
                1 // Number of items per page (adjust as needed)
            );
        
            // Determine the template based on the user's role
            if ($security->isGranted('ROLE_ADMIN')) {
                $template = 'user/index.html.twig';
            } elseif ($security->isGranted('ROLE_TUTEUR')) {
                $template = 'user/indexTuteurSide.html.twig';
            } elseif ($security->isGranted('ROLE_USER')) {
                $template = 'user/indexUserSide.html.twig';
            } else {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }
        
            // Pass the paginated result to the template
            return $this->render($template, [
                'users' => $users, // Pass the paginated result
            ]);
        }





// src/Controller/UserController.php

#[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
public function dashboard(UserRepository $userRepository): Response
{
    // Fetch counts for each role
    $totalUsers = $userRepository->countUsersByRole('ROLE_USER');
    $totalTuteurs = $userRepository->countUsersByRole('ROLE_TUTEUR');
    $totalAdmins = $userRepository->countUsersByRole('ROLE_ADMIN');

    // Render the dashboard template and pass the counts
    return $this->render('dashboard/index.html.twig', [
        'totalUsers' => $totalUsers,
        'totalTuteurs' => $totalTuteurs,
        'totalAdmins' => $totalAdmins,
    ]);
}






        #[IsGranted('ROLE_ADMIN')]
        #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
        public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): Response
        {
            // Create a new User object
            $user = new User();
        
            // Check if the form is submitted
            if ($request->isMethod('POST')) {
                // Update the user object with the form data
                $user->setPrenom($request->request->get('prenom'));
                $user->setNom($request->request->get('nom'));
                $user->setEmail($request->request->get('email'));
                $user->setPassword($request->request->get('password')); // Password will be hashed later
                $user->setNumTel($request->request->get('numTel'));
                $user->setDateDeNaissance(new \DateTime($request->request->get('dateDeNaissance')));
                $user->setAdresse($request->request->get('adresse'));
                $user->setBio($request->request->get('bio'));
        
                // Validate the user object
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    // If there are validation errors, pass them to the template
                    $errorMessages = [];
                    foreach ($errors as $error) {
                        $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                    }
        
                    return $this->render('user/new.html.twig', [
                        'errors' => $errorMessages, // Pass errors to the template
                        'formData' => $request->request->all(), // Pass submitted form data back to the form
                    ]);
                }
        
                // Hash the password before saving
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
        
                // Save the new user to the database
                $entityManager->persist($user);
                $entityManager->flush();
        
                // Add a success message
                $this->addFlash('success', 'l"utilisateur a été crée avec succées!');
        
                // Redirect to a relevant page (e.g., user list or profile)
                return $this->redirectToRoute('app_user_index');
            }
        
            // If the form is not submitted, render the form without errors
            return $this->render('user/new.html.twig', [
                'errors' => [], // No errors initially
                'formData' => [], // No form data initially
            ]);

       
    }






        

        #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TUTEUR")'), message: 'Unauthorized access!')]

        #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
        public function show(User $user): Response
        {
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }


     
    
     
        #[IsGranted('ROLE_ADMIN')]      
        #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
        public function edit(
            Request $request,
            User $user,
            EntityManagerInterface $entityManager,
            ValidatorInterface $validator,
            UserPasswordHasherInterface $passwordHasher
        ): Response {
            // Check if the form is submitted
            if ($request->isMethod('POST')) {
                // Update the user object with the form data
                $user->setPrenom($request->request->get('prenom'));
                $user->setNom($request->request->get('nom'));
                $user->setEmail($request->request->get('email'));
                $user->setNumTel($request->request->get('numTel'));
                $user->setDateDeNaissance(new \DateTime($request->request->get('dateDeNaissance')));
                $user->setAdresse($request->request->get('adresse'));
                $user->setBio($request->request->get('bio'));
        
                // Handle password update (only if a new password is provided)
                $newPassword = $request->request->get('password');
                if (!empty($newPassword)) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedPassword);
                }
        
                // Validate the user object
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    // If there are validation errors, pass them to the template
                    $errorMessages = [];
                    foreach ($errors as $error) {
                        $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                    }
        
                    return $this->render('user/edit.html.twig', [
                        'user' => $user,
                        'errors' => $errorMessages, // Pass errors to the template
                        'formData' => $request->request->all(), // Pass submitted form data back to the form
                    ]);
                }
        
                // Save changes to the database
                $entityManager->flush();
        
                // Add a success message
                $this->addFlash('success', 'utilisateur modifié!');
        
                // Redirect to the user list
                return $this->redirectToRoute('app_user_index');
            }
        
            // If the form is not submitted, render the form with the current user data
            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'errors' => [], // No errors initially
                'formData' => [
                    'prenom' => $user->getPrenom(),
                    'nom' => $user->getNom(),
                    'email' => $user->getEmail(),
                    'numTel' => $user->getNumTel(),
                    'dateDeNaissance' => $user->getDateDeNaissance() ? $user->getDateDeNaissance()->format('Y-m-d') : '',
                    'adresse' => $user->getAdresse(),
                    'bio' => $user->getBio(),
                ], // Pre-fill form data with current user data
            ]);
        }
        
    
        
        #[IsGranted('ROLE_ADMIN')]   
        #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
        public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'un utilisateur a été supprimé!');
    
            }
                        
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    
    
        
    }
    
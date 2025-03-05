<?php
// src/Controller/ProfileController.php
// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\UserType;
use App\Entity\Tuteur;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        // Ensure the user is logged in
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Get the logged-in user
        $user = $this->getUser();


        
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'errors' => [], // Initialize errors as an empty array
            'activeTab' => 'personal', // Indicate the active tab
        ]);
    }

    #[Route('/profile/upload-picture', name: 'app_upload_profile_pic', methods: ['POST'])]
    public function uploadProfilePicture(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        // Ensure the user is logged in
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Get the logged-in user
        $user = $this->getUser();

        

        // Debug the user object
        if (!$user instanceof User) {
            throw new \RuntimeException('Expected a User object, got ' . get_class($user));
        }

        // Handle the file upload
        $uploadedFile = $request->files->get('profilePic');

        if ($uploadedFile instanceof UploadedFile) {
            // Generate a unique filename
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            // Move the file to the uploads directory
            $uploadedFile->move(
                $this->getParameter('profile_pictures_directory'),
                $newFilename
            );

            // Update the user's profile picture
            $user->setPhotoDeProfil($newFilename);
            $entityManager->flush();

            // Add a success message
            $this->addFlash('success', 'Profile picture updated successfully!');
        } else {
            // Add an error message
            $this->addFlash('error', 'Invalid file upload.');
        }

        // Redirect back to the profile page
        return $this->redirectToRoute('app_profile');
    }





    #[Route('/profile/update', name: 'app_update_profile', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Ensure the user is logged in
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
        // Get the logged-in user
        $user = $this->getUser();
    
        // Debug the user object
        if (!$user instanceof User) {
            throw new \RuntimeException('Expected a User object, got ' . get_class($user));
        }
    
        // Create a clone of the user object to hold the form data temporarily
        $userData = clone $user;
    
        // Update the cloned user object with the form data
        $userData->setPrenom($request->request->get('prenom'));
        $userData->setNom($request->request->get('nom'));
        $userData->setEmail($request->request->get('email'));
        $userData->setNumTel($request->request->get('numTel'));
        $userData->setDateDeNaissance(new \DateTime($request->request->get('dateDeNaissance')));
        $userData->setAdresse($request->request->get('adresse'));
        $userData->setBio($request->request->get('bio'));
    
        // Update specialite if the user is a tutor
        if ($userData instanceof Tuteur) {
            $userData->setSpecialite($request->request->get('specialite'));
        }
    
        // Validate the cloned user object
        $errors = $validator->validate($userData);


         // Check if the email has changed
    if ($userData->getEmail() === $user->getEmail()) {
        // If the email hasn't changed, remove the email validation error (if any)
        $errors = array_filter(iterator_to_array($errors), function ($error) {
            return $error->getPropertyPath() !== 'email';
        });
    }
    
        if (count($errors) > 0) {
            // If there are validation errors, pass them to the template
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
    
            return $this->render('profile/index.html.twig', [
                'user' => $user, // Pass the original user object, not the cloned one
                'errors' => $errorMessages, // Pass errors to the template
                'activeTab' => 'personal', // Indicate the active tab
            ]);
        }
    
        // If validation passes, update the original user object with the validated data
        $user->setPrenom($userData->getPrenom());
        $user->setNom($userData->getNom());
        $user->setEmail($userData->getEmail());
        $user->setNumTel($userData->getNumTel());
        $user->setDateDeNaissance($userData->getDateDeNaissance());
        $user->setAdresse($userData->getAdresse());
        $user->setBio($userData->getBio());
    
        if ($user instanceof Tuteur) {
            $user->setSpecialite($request->request->get('specialite'));        }
    
        // Save changes to the database
        $entityManager->flush();
    
        // Add a success message
        $this->addFlash('success', 'Profile updated successfully!');
    
        // Redirect back to the profile page
        return $this->redirectToRoute('app_profile');

        
    }




    #[Route('/profile/security', name: 'app_profile_security', methods: ['GET', 'POST'])]
    public function security(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        // Ensure the user is logged in
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            throw new \RuntimeException('Expected a User object, got ' . get_class($user));
        }
    
        // Initialize error messages
        $errors = [];
    
        if ($request->isMethod('POST')) {
            // Get form data
            $oldPassword = $request->request->get('oldPassword');
            $newPassword = $request->request->get('newPassword');
            $confirmPassword = $request->request->get('confirmPassword');
    
            // Validate old password
            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $errors['oldPassword'] = 'The old password is incorrect.';
            }
    
            // Validate new password and confirmation
            if (empty($newPassword)) {
                $errors['newPassword'] = 'The new password cannot be empty.';
            } elseif (strlen($newPassword) < 6) {
                $errors['newPassword'] = 'The new password must be at least 6 characters long.';
            }
    
            if ($newPassword !== $confirmPassword) {
                $errors['confirmPassword'] = 'The new password and confirmation do not match.';
            }
    
            // If there are no errors, update the password
            if (empty($errors)) {
                // Set the new password (hashed)
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $entityManager->flush();
    
                // Add a success message
                $this->addFlash('success', 'Password updated successfully!');
    
                // Redirect back to the security page
                return $this->redirectToRoute('app_profile_security');
            }
        }
    
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'errors' => $errors,
            'activeTab' => 'security', // Indicate the active tab
        ]);
    }
}


<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class ProfileController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        // Get the logged-in user
        $user = $this->security->getUser();

        // Check if the user is logged in
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        // Render the profile page with the user's data
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);

        // In your controller or service
        $uploadedFile = $form['photoDeProfil']->getData();
        if ($uploadedFile) {
            $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $this->getParameter('profile_pictures_directory'),
                $newFilename
            );
            $user->setPhotoDeProfil($newFilename);
        }
    }
}
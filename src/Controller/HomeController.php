<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffreRepository;

use App\Repository\UserRepository;

final class HomeController extends AbstractController{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home_redirect')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            
        ]);
    }
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(UserRepository $userRepository): Response
    {
        // Fetch tutors with the role 'ROLE_TUTEUR'
        $tuteurs = $userRepository->findByRole('ROLE_TUTEUR');
        $allTuteurs = $userRepository->findByRole('ROLE_TUTEUR');

        // Limit the results to the first 4 tutors
        $tuteurs = array_slice($allTuteurs, 0, 4);
    
    
        return $this->render('home/index.html.twig', [
            'tuteurs' => $tuteurs,
        ]);
    }

}







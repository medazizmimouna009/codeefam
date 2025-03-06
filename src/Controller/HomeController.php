<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\CategorieRepository;
use App\Repository\OffreRepository;
use App\Repository\CoursRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home_redirect')]
    public function index(
        UserRepository $userRepository,
        CategorieRepository $categorieRepository,
        OffreRepository $offreRepository,
        CoursRepository $coursRepository
    ): Response
    {
        // Fetch all categories
        $categories = $categorieRepository->findAll();

        // Fetch tutors with the role 'ROLE_TUTEUR'
        $allTuteurs = $userRepository->findByRole('ROLE_TUTEUR');

        // Limit the results to the first 4 tutors
        $tuteurs = array_slice($allTuteurs, 0, 4);

        // Fetch all offers
        $offres = $offreRepository->findAll();

        // Fetch all courses (if needed)
        $cours = $coursRepository->findAll(); // You can customize this query if needed

        // Pass all data to the view
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'categories' => $categories,
            'tuteurs' => $tuteurs,
            'offres' => $offres,
            'cours' => $cours, // Add this if you need to use courses in the template
        ]);
    }
}
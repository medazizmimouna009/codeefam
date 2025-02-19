<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OffreRepository;


final class HomeController extends AbstractController{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home_redirect')]
    public function index(OffreRepository $offreRepository): Response
    {
        // Retrieve all offers from the database
        $offres = $offreRepository->findAll();

        // Render the index page and pass the offers to the template
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'offres' => $offres,
        ]);
    }
    }
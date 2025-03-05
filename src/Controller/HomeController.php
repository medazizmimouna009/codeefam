<?php
// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategorieRepository; // Importez le repository des catégories

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home_redirect')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        // Récupérer toutes les catégories depuis la base de données
        $categories = $categorieRepository->findAll();

        // Passer les catégories à la vue
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'categories' => $categories, // Ajoutez les catégories ici
        ]);
    }
}
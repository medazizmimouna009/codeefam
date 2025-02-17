<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexTuteurTuteurSideController extends AbstractController{
    #[Route('/index/tuteur/tuteur/side', name: 'app_index_tuteur_tuteur_side')]
    public function index(): Response
    {
        return $this->render('tuteur/indexTuteurTuteurSide.html.twig', [
            'controller_name' => 'IndexTuteurTuteurSideController',
        ]);
    }
}

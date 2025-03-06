<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexTuteurSideController extends AbstractController{
    #[Route('/index/tuteur/side', name: 'app_index_tuteur_side')]
    public function index(): Response
    {
        return $this->render('user/indexTuteurSide.html.twig', [
            'controller_name' => 'IndexTuteurSideController',
        ]);
    }
}

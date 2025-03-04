<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexTuteurUserSideController extends AbstractController{
    #[Route('/index/tuteur/user/side', name: 'app_index_tuteur_user_side')]
    public function index(): Response
    {
        return $this->render('tuteur/indexTuteurUserSide.html.twig', [
            'controller_name' => 'IndexTuteurUserSideController',
        ]);
    }
}

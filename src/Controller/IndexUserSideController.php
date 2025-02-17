<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexUserSideController extends AbstractController{
    #[Route('/index/user/side', name: 'app_index_user_side')]
    public function index(): Response
    {
        return $this->render('user/indexUserSide.html.twig', [
            'controller_name' => 'IndexUserSideController',
        ]);
    }
}

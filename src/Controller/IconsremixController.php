<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IconsremixController extends AbstractController{
    #[Route('/iconsremix', name: 'app_iconsremix')]
    public function index(): Response
    {
        return $this->render('iconsremix/index.html.twig', [
            'controller_name' => 'IconsremixController',
        ]);
    }
}

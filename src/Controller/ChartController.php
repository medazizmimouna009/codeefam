<?php
// src/Controller/ChartController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartController extends AbstractController
{
    #[Route('/chart', name: 'app_chart')]
// src/Controller/ChartController.php
public function index(): Response
{
    $chartData = [
        'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        'datasets' => [
            [
                'label' => 'Sales',
                'data' => [65, 59, 80, 81, 56, 55, 40],
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
            ],
        ],
    ];

    return $this->render('chart/index.html.twig', [
        'chartData' => $chartData,
    ]);
}
}

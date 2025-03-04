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
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        // Créer un graphique de type Pie
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        // Définir les données pour le Pie Chart
        $chart->setData([
            'labels' => ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => [300, 50, 100, 40, 120, 80], // Valeurs pour chaque secteur
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)'
                    ],
                    'hoverOffset' => 4 // Effet de survol
                ],
            ],
        ]);

        // Options du graphique (optionnel)
        $chart->setOptions([
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Pie Chart Example'
                ]
            ]
        ]);

        return $this->render('chart/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}
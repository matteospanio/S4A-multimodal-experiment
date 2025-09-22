<?php

namespace App\Controller;

use App\Entity\Trial\FlavorToMusicTrial;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Entity\Trial\Trial;
use App\Form\F2MTrialType;
use App\Form\M2FTrialType;
use App\Repository\FlavorRepository;
use App\Repository\FlavorToMusicTrialRepository;
use App\Repository\MusicToFlavorTrialRepository;
use App\Service\DataCollector;
use App\Service\Mathematician;
use App\Service\StimuliManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly StimuliManager $stimuliManager,
        private readonly DataCollector $dataCollector,
        private readonly Mathematician $mathematician,
    )
    {
    }

    #[Route('/task/{type}', name: 'app_task', requirements: ['type' => '\d+'], methods: ['GET'])]
    public function index(int $type = 1): Response
    {
        $task = $this->getTask($type);
        $trial = $this->stimuliManager->getNextTrial($task);

        $form = match($task) {
            Trial::MUSICS2SMELL => $this->createForm(M2FTrialType::class, $trial),
            Trial::SMELLS2MUSIC => $this->createForm(F2MTrialType::class, $trial),
            default => throw $this->createNotFoundException('Invalid task type'),
        };

        return $this->render('task/index.html.twig', [
            'task_type' => $type,
            'trial' => $trial,
            'form' => $form,
        ]);
    }

    #[Route('/task/{type}/{id:trial}/submit', name: 'app_task_submit', requirements: ['type' => '\d+'], methods: ['POST'])]
    public function submit(Request $request, int $type, Trial $trial): Response
    {
        $task = $this->getTask($type);

        $form = match($task) {
            Trial::MUSICS2SMELL => $this->createForm(M2FTrialType::class, $trial),
            Trial::SMELLS2MUSIC => $this->createForm(F2MTrialType::class, $trial),
            default => throw $this->createNotFoundException('Invalid task type'),
        };

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choice = $form->get('choice')->getData();
            $this->dataCollector->recordVote($choice, $trial);
            return $this->redirectToRoute('app_task_results', ['type' => $type, 'id' => $trial->getId()]);
        }

        return $this->redirectToRoute('app_task', ['type' => $type]);
    }

    #[Route('/task/{type}/{id:trial}/results', name: 'app_task_results', requirements: ['type' => '\d+'], methods: ['GET'])]
    public function showResults(
        int $type,
        Trial $trial,
        ChartBuilderInterface $chartBuilder,
        FlavorRepository $flavorRepository,
        MusicToFlavorTrialRepository $musicToFlavorTrialRepository,
        FlavorToMusicTrialRepository $flavorToMusicTrialRepository,
    ): Response
    {
        $task = $this->getTask($type);
        $choice = $trial->getChoice();
        $barChart = null;

        if ($task === Trial::MUSICS2SMELL) {
            // Task 1: Show double bar chart for track choices given a perfume
            assert($trial instanceof MusicToFlavorTrial);
            $success = $choice === $trial->getFlavor();

            $stats = $this->mathematician->getMusicToFlavorStatistics($trial->getFlavor());

            if (!empty($stats['labels'])) {
                $barChart = $chartBuilder->createChart(Chart::TYPE_BAR);
                $barChart->setOptions([
                    'indexAxis' => 'y',
                    'responsive' => true,
                    'plugins' => [
                        'title' => [
                            'display' => true,
                            'text' => sprintf('Track choices for perfume: %s %s',
                                $trial->getFlavor()->getIcon(),
                                $trial->getFlavor()->getName()
                            ),
                        ],
                        'legend' => [
                            'display' => false,
                        ],
                    ],
                    'scales' => [
                        'x' => [
                            'beginAtZero' => true,
                            'title' => [
                                'display' => true,
                                'text' => 'Percentage (%)',
                            ],
                        ],
                    ],
                ]);
                $barChart->setData([
                    'labels' => $stats['labels'],
                    'datasets' => [
                        [
                            'label' => 'Percentage of participants',
                            'backgroundColor' => $stats['backgroundColors'],
                            'borderColor' => $stats['borderColors'],
                            'borderWidth' => 2,
                            'data' => $stats['data'],
                        ],
                    ],
                ]);
            }
        } else {
            // Task 2: Show bar chart for perfume choices given a track
            assert($trial instanceof FlavorToMusicTrial);
            $success = $choice === $trial->getSong();

            $stats = $this->mathematician->getFlavorToMusicStatistics($trial->getSong());

            if (!empty($stats['labels'])) {
                $barChart = $chartBuilder->createChart(Chart::TYPE_BAR);
                $barChart->setOptions([
                    'indexAxis' => 'y',
                    'responsive' => true,
                    'plugins' => [
                        'title' => [
                            'display' => true,
                            'text' => sprintf('Perfume choices for track: Song #%d (%s)',
                                $trial->getSong()->getId(),
                                $trial->getSong()->getFlavor()->getName()
                            ),
                        ],
                        'legend' => [
                            'display' => false,
                        ],
                    ],
                    'scales' => [
                        'x' => [
                            'beginAtZero' => true,
                            'title' => [
                                'display' => true,
                                'text' => 'Percentage (%)',
                            ],
                        ],
                    ],
                ]);
                $barChart->setData([
                    'labels' => $stats['labels'],
                    'datasets' => [
                        [
                            'label' => 'Percentage of participants',
                            'backgroundColor' => $stats['backgroundColors'],
                            'borderColor' => $stats['borderColors'],
                            'borderWidth' => 2,
                            'data' => $stats['data'],
                        ],
                    ],
                ]);
            }
        }

        return $this->render('task/results.html.twig', [
            'task' => $task,
            'trial' => $trial,
            'barChart' => $barChart,
            'success' => $success ?? false,
        ]);
    }

    private function getTask(int $type): string
    {
        return match ($type) {
            1 => Trial::MUSICS2SMELL,
            2 => Trial::SMELLS2MUSIC,
            default => throw $this->createNotFoundException('Invalid task type'),
        };
    }
}

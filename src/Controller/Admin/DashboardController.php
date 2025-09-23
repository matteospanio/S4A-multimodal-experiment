<?php

namespace App\Controller\Admin;

use App\Entity\Experiment;
use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Task;
use App\Entity\Trial\FlavorToMusicTrial;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Entity\Trial\Trial;
use App\Repository\SongRepository;
use App\Repository\TaskRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin/{_locale}', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly SongRepository $songRepository,
        private readonly TaskRepository $taskRepository,
        private readonly ChartBuilderInterface $chartBuilder
    )
    {
    }

    public function index(): Response
    {
        // Existing song by flavor chart
        $count = $this->songRepository->findGroupedByFlavor();
        $labels = array_column($count, 'flavor');
        $data = array_column($count, 'song_count');

        $songByFlavor = $this->chartBuilder->createChart(Chart::TYPE_PIE)
            ->setData([
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Songs by Flavor',
                        'data' => $data,
                        'backgroundColor' => ['#45d73b', '#ff8700', '#290a00', '#FFFF00'],
                    ],
                ],
            ])
            ->setOptions([
                'maintainAspectRatio' => false,
            ])
        ;

        // Get task statistics
        $taskCounts = $this->taskRepository->getTaskTypeCounts();
        $taskTypeCountMap = [];
        foreach ($taskCounts as $taskCount) {
            $taskTypeCountMap[$taskCount['type']] = $taskCount['trial_count'];
        }

        // Get hourly statistics for line chart
        $hourlyStats = $this->taskRepository->getHourlyTaskStats();

        // Prepare data for the line chart - last 12 hours
        $hoursLabels = [];
        $music2aromaData = [];
        $aroma2musicData = [];

        $now = new \DateTime();
        for ($i = 11; $i >= 0; $i--) {
            $hour = clone $now;
            $hour->sub(new \DateInterval("PT{$i}H"));
            $hourLabel = $hour->format('H:00');
            $hoursLabels[] = $hourLabel;

            // Initialize data for this hour
            $music2aromaCount = 0;
            $aroma2musicCount = 0;

            // Find matching data for this hour
            foreach ($hourlyStats as $stat) {
                $statHour = sprintf('%02d:00', $stat['hour_created']);
                $statDate = $stat['date_created'];

                if ($statHour === $hourLabel && $statDate === $hour->format('Y-m-d')) {
                    if ($stat['type'] === 'music2aroma') {
                        $music2aromaCount = $stat['trial_count'];
                    } elseif ($stat['type'] === 'aroma2music') {
                        $aroma2musicCount = $stat['trial_count'];
                    }
                }
            }

            $music2aromaData[] = $music2aromaCount;
            $aroma2musicData[] = $aroma2musicCount;
        }

        // Create line chart
        $hourlyTaskChart = $this->chartBuilder->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => $hoursLabels,
                'datasets' => [
                    [
                        'label' => 'Music to Aroma Tasks',
                        'data' => $music2aromaData,
                        'borderColor' => '#ff8700',
                        'backgroundColor' => 'rgba(255, 135, 0, 0.1)',
                        'fill' => false,
                        'tension' => 0.1,
                    ],
                    [
                        'label' => 'Aroma to Music Tasks',
                        'data' => $aroma2musicData,
                        'borderColor' => '#45d73b',
                        'backgroundColor' => 'rgba(69, 215, 59, 0.1)',
                        'fill' => false,
                        'tension' => 0.1,
                    ],
                ],
            ])
            ->setOptions([
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'title' => [
                            'display' => true,
                            'text' => 'Number of Completed Trials',
                        ],
                    ],
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Time (Last 12 Hours)',
                        ],
                    ],
                ],
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Trials Completed per Hour by Task Type',
                    ],
                ],
            ])
        ;

        return $this->render('admin/dashboard.html.twig', [
            'songByFlavor' => $songByFlavor,
            'hourlyTaskChart' => $hourlyTaskChart,
            'music2aromaCount' => $taskTypeCountMap['music2aroma'] ?? 0,
            'aroma2musicCount' => $taskTypeCountMap['aroma2music'] ?? 0,
            'totalTrials' => array_sum($taskTypeCountMap),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('S4A Experiment')
            ->setLocales([
                'en' => '🇬🇧 English',
                'it' => '🇮🇹 Italiano',
            ])
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addAssetMapperEntry('admin')
            ->useCustomIconSet('bi')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'speedometer2');
        yield MenuItem::section('Stimuli');
        yield MenuItem::linkToCrud('Sounds', 'music-note-beamed', Song::class);
        yield MenuItem::linkToCrud('Flavors', 'flask-florence', Flavor::class);
        yield MenuItem::section('Experiments');
        yield MenuItem::linkToCrud('Experiments', 'flask', Experiment::class);
        yield MenuItem::linkToCrud('Tasks', 'list-task', Task::class);
        // yield MenuItem::linkToCrud('Trials', 'check2-square', Trial::class);
        yield MenuItem::subMenu('Trials', 'check2-square')->setSubItems([
            MenuItem::linkToCrud('Music to Aroma', 'arrow-right-circle', MusicToFlavorTrial::class),
            MenuItem::linkToCrud('Aroma to Music', 'arrow-left-circle', FlavorToMusicTrial::class),
        ]);
        yield MenuItem::section();
        yield MenuItem::linkToUrl('Home', 'house', $this->generateUrl('app_home'));
        yield MenuItem::linkToLogout('Logout', 'door-open');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}

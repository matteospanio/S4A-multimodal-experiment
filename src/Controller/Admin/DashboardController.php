<?php

namespace App\Controller\Admin;

use App\Entity\Experiment;
use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Task;
use App\Entity\Trial\Trial;
use App\Repository\SongRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly SongRepository $songRepository,
        private readonly ChartBuilderInterface $chartBuilder
    )
    {
    }

    public function index(): Response
    {
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

        return $this->render('admin/dashboard.html.twig', [
            'songByFlavor' => $songByFlavor,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('S4A Experiment')
            ->setLocales([
                'en' => 'English',
                'it' => 'Italiano',
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
        yield MenuItem::linkToCrud('Trials', 'check2-square', Trial::class);
        yield MenuItem::section();
        yield MenuItem::linkToRoute('Home', 'house', 'app_home');
        yield MenuItem::linkToLogout('Logout', 'door-open');
    }
}

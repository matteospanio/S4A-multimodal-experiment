<?php

namespace App\Controller\Admin;

use App\Entity\Experiment;
use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Task;
use App\Entity\Trial\Trial;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly ChartBuilderInterface $chartBuilder)
    {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('S4A Multimodal Experiment')
            ->setLocales([
                'en' => 'English',
                'it' => 'Italiano',
            ])
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::section('Stimuli');
        yield MenuItem::linkToCrud('Sounds', 'fa fa-music', Song::class);
        yield MenuItem::linkToCrud('Flavors', 'fa fa-music', Flavor::class);
        yield MenuItem::section('Experiments');
        yield MenuItem::linkToCrud('Experiments', 'fa fa-list', Experiment::class);
        yield MenuItem::linkToCrud('Tasks', 'fa fa-list', Task::class);
        yield MenuItem::linkToCrud('Trials', 'fa fa-list', Trial::class);
        yield MenuItem::section();
        yield MenuItem::linkToRoute('Home', 'fa fa-home', 'app_home');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}

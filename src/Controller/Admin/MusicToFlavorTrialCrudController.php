<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Trial\MusicToFlavorTrial;
use App\Repository\MusicToFlavorTrialRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class MusicToFlavorTrialCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly MusicToFlavorTrialRepository $repository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return MusicToFlavorTrial::class;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $exportAction = Action::new('exportCsv', 'Export CSV', 'fa fa-download')
            ->linkToRoute('admin_music_to_flavor_trial_export_csv')
            ->createAsGlobalAction();

        return parent::configureActions($actions)
            ->disable(Action::EDIT)
            ->add(Crud::PAGE_INDEX, $exportAction)
        ;
    }

    #[\Override]
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('createdAt')->setLabel('Date'))
        ;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $common = [
            TextField::new('timeInterval')
        ];

        if ($pageName === Crud::PAGE_INDEX) {
            return array_merge([
                IdField::new('id'),
                TextField::new('intendedSong'),
                BooleanField::new('doesMatch')->renderAsSwitch(false),
            ], $common);
        }

        return array_merge([
            CollectionField::new('songs'),
            AssociationField::new('flavor'),
            AssociationField::new('choice'),
            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
        ], $common
        );
    }

    #[Route('/admin/music-to-flavor-trial/export-csv', name: 'admin_music_to_flavor_trial_export_csv')]
    public function exportCsv(Request $request): Response
    {
        // Support date parameter for filtering by specific date
        // Usage: /admin/music-to-flavor-trial/export-csv?date=2024-09-30
        $date = $request->query->get('date');
        
        $qb = $this->repository->createQueryBuilder('t')
            ->leftJoin('t.flavor', 'f')
            ->leftJoin('t.choice', 'c')
            ->leftJoin('t.task', 'task')
            ->orderBy('t.createdAt', 'DESC');

        // Filter by date if provided
        if ($date) {
            $dateObj = new \DateTime($date);
            $qb->andWhere('DATE(t.createdAt) = :date')
                ->setParameter('date', $dateObj->format('Y-m-d'));
        }

        $trials = $qb->getQuery()->getResult();

        $response = new StreamedResponse(function () use ($trials) {
            $handle = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($handle, [
                'ID',
                'Flavor',
                'Choice (Song)',
                'Match',
                'Time Interval',
                'Task',
                'Created At',
                'Updated At',
            ]);

            // CSV rows
            foreach ($trials as $trial) {
                fputcsv($handle, [
                    $trial->getId(),
                    $trial->getFlavor()?->getName() ?? '',
                    $trial->getChoice()?->__toString() ?? '',
                    $trial->doesMatch() ? 'Yes' : 'No',
                    $trial->getTimeInterval() ?? '',
                    $trial->getTask()?->__toString() ?? '',
                    $trial->getCreatedAt()?->format('Y-m-d H:i:s') ?? '',
                    $trial->getUpdatedAt()?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        });

        $filename = 'music_to_flavor_trials_' . ($date ?? 'all') . '_' . date('Y-m-d_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}

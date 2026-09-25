<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Trial\FlavorToMusicTrial;
use App\Repository\FlavorToMusicTrialRepository;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @extends AbstractCrudController<FlavorToMusicTrial>
 */
class FlavorToMusicTrialCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly FlavorToMusicTrialRepository $repository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return FlavorToMusicTrial::class;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $exportAction = Action::new('exportCsv', 'Export CSV', 'download')
            ->linkToRoute('admin_flavor_to_music_trial_export_csv')
            ->createAsGlobalAction();

        return parent::configureActions($actions)
            ->disable(Action::EDIT)
            ->add(Crud::PAGE_INDEX, $exportAction);
    }

    #[\Override]
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('createdAt')->setLabel('Date'))
        ;
    }

    #[Route('/admin/flavor-to-music-trial/export-csv', name: 'admin_flavor_to_music_trial_export_csv')]
    public function exportCsv(Request $request): Response
    {
        $date = $request->query->get('date');

        $queryBuilder = $this->repository->createQueryBuilder('trial')
            ->leftJoin('trial.song', 'song')
            ->leftJoin('trial.choice', 'choice')
            ->leftJoin('trial.task', 'task')
            ->orderBy('trial.createdAt', 'DESC');

        if ($date) {
            $startOfDay = new \DateTimeImmutable($date);
            $queryBuilder
                ->andWhere('trial.createdAt >= :startOfDay')
                ->andWhere('trial.createdAt < :startOfNextDay')
                ->setParameter('startOfDay', $startOfDay)
                ->setParameter('startOfNextDay', $startOfDay->modify('+1 day'));
        }

        $trials = $queryBuilder->getQuery()->getResult();

        $response = new StreamedResponse(function () use ($trials) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Song',
                'Choice (Flavor)',
                'Match',
                'Time Interval',
                'Task',
                'Created At',
                'Updated At',
            ]);

            foreach ($trials as $trial) {
                fputcsv($handle, [
                    $trial->getId(),
                    $trial->getSong()?->__toString() ?? '',
                    $trial->getChoice()?->getName() ?? '',
                    $trial->doesMatch() ? 'Yes' : 'No',
                    $trial->getTimeInterval() ?? '',
                    $trial->getTask()?->__toString() ?? '',
                    $trial->getCreatedAt()?->format('Y-m-d H:i:s') ?? '',
                    $trial->getUpdatedAt()?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        });

        $filename = 'flavor_to_music_trials_' . ($date ?? 'all') . '_' . date('Y-m-d_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
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
                TextField::new('intendedFlavor'),
                BooleanField::new('doesMatch')->renderAsSwitch(false),
            ], $common);
        }

        return array_merge([
            CollectionField::new('flavors'),
            AssociationField::new('song'),
            AssociationField::new('choice'),
            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
            ], $common
        );
    }
}

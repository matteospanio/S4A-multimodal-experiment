<?php

namespace App\Command;

use App\Service\DataCollector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(
    name: 'app:trials:clean',
    description: 'Delete all trials without recorded choice.',
)]
#[AsCronTask('#hourly', arguments: '--no-interaction')]
class TrialsCleanCommand extends Command
{
    public function __construct(
        private readonly DataCollector $dataCollector,
    )
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        InputInterface $input,
    ): int
    {
        $io = new SymfonyStyle($input, $output);
        $confirm = $io->ask('This will delete all trials without recorded choice. Are you sure you want to proceed? (yes/no)', 'yes');
        if (strtolower($confirm) !== 'yes') {
            $io->warning('Operation cancelled. No trials were deleted.');
            return Command::SUCCESS;
        }

        $this->dataCollector->deleteEmptyTrials();
        $io->success('All trials without recorded choice have been deleted.');

        return Command::SUCCESS;
    }
}

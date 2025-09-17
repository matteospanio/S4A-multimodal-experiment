<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:new:user',
    description: 'Create a new user in the system.',
    help: 'This command allows you to create a user in the system. You can optionally set the user as an admin using the --admin option.',
)]
class NewUserCommand extends Command
{
    public function __construct(private readonly UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if (null === $input->getOption('username')) {
            $username = $io->ask('Please provide a username');
            $input->setOption('username', $username);
        }

        if ($input->getOption('random')) {
            $password = bin2hex(random_bytes(4)); // Generate a random 8-character password
            $io->writeln('Generated random password: ' . $password);
            $input->setOption('password', $password);
        } else if (null === $input->getOption('password')) {
            $password = $io->askHidden('Please provide a password');
            $input->setOption('password', $password);
        }
    }

    public function __invoke(
        OutputInterface $output,
        #[Option('The username to be identified.', shortcut: 'u')] ?string $username = null,
        #[Option('The password for the user account.', shortcut: 'p')] ?string $password = null,
        #[Option('If set, the user is created as an admin', shortcut: 'a')] bool $admin = false,
        #[Option('If set, generate a random password for the user', shortcut:  'r') ] bool $random = false,
    ): int
    {
        if (null === $username || null === $password) {
            throw new \InvalidArgumentException('Username, email, and password are required to create a new user.');
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('The password must be at least 6 characters long.');
        }

        try {
            $this->userRepository->create($username, $password, $admin);
        } catch (\Exception $e) {
            $output->writeln('<error>Error creating user: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>User successfully created!</info>');

        return Command::SUCCESS;
    }
}

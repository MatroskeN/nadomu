<?php

namespace App\Command;

use App\Entity\User;
use App\Services\RandomizeServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'user:create';
    protected static $defaultDescription = 'Create user';
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct($name = null, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getOption('password') ?? RandomizeServices::generateString(8);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->caution('Email is invalid!');

            return Command::FAILURE;
        }

        $user_exists = $this->em->getRepository(User::class)->findByEmail($email);

        if ($user_exists) {
            $io->caution('User already exists!');

            return Command::FAILURE;
        }

        if ($email && $password) {
            $user = new User();
            $user->setEmail($email)
                ->setPassword($this->userPasswordHasher->hashPassword($user, $password))
                ->setFirstName('')
                ->setLastName('');

            $this->em->persist($user);
            $this->em->flush();

            $io->success("User successfully created!\r\nEmail: $email\r\nPassword: $password");
        }

        return Command::SUCCESS;
    }
}

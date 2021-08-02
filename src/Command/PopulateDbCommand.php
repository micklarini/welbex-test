<?php

namespace App\Command;

//use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

/*#[AsCommand(
    name: 'app:populate-db',
    description: 'Pouplate database table with auto generated values.'
)]*/
class PopulateDbCommand extends Command
{
    protected static $defaultName = 'app:populate-db';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Pouplate database tables with auto generated values.')
            ->addOption('quantity', null, InputOption::VALUE_REQUIRED, 'Number of values to seed', 100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $input->getOption('quantity'); $i++) {
            $entry = new \App\Entity\ListEntry();
            $entry->setEntryDate($faker->dateTimeBetween('-2 years', 'now'));
            $entry->setName($faker->company());
            $entry->setQuantity($faker->numberBetween(1, 1000));
            $entry->setDistance($faker->randomFloat(3, 0.1, 10000));
            $this->entityManager->persist($entry);
        }
        $this->entityManager->flush();

        $io->success('Database population completed');

        return Command::SUCCESS;
    }
}

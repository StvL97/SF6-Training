<?php

namespace App\Command;

use App\Service\MovieImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-movie',
    description: 'Imports the data for a movie from Omdb',
)]
class ImportMovieCommand extends Command
{
    public function __construct(private readonly MovieImporter $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'title',
                InputArgument::REQUIRED,
                'Title of the movie which you want to import'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $title = $input->getArgument('title');
        $movie = $this->importer->importByTitle($title);

        if ($movie === null) {
            $io->error('Movie could not be imported.');
            return Command::FAILURE;
        }

        $table = new Table($output);
        $table->setHeaders(['Key', 'Value'])
            ->setRows([
                    ['title', $movie->getTitle()],
                    ['plot', $movie->getPlot()],
                    ['release date', $movie->getReleasedAt()->format('d-m-Y')]

            ]);
        $table->render();

        $io->success(sprintf('Import successful'));

        return Command::SUCCESS;
    }
}

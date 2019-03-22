<?php

namespace App\Commands;

use Spatie\Valuestore\Valuestore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfig extends Command
{
    private $inputFile;

    private $outputFile;

    public function __construct()
    {
        parent::__construct();

        $this->inputFile = base_path('settings.ini');
        $this->outputFile = base_path('config/slack-house.json');
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('config:generate')
            ->setDescription('Generates a static config file from your ./settings.ini configuration')
            ->setHelp('This command will generate your config file');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $settings = parse_ini_file($this->inputFile, true, INI_SCANNER_TYPED);

        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }

        Valuestore::make($this->outputFile, $settings);

        $output->writeln('Config file generated.');
    }
}

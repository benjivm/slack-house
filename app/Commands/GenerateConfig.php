<?php

namespace App\Commands;

use Gmask\ConfigGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfig extends Command
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('config:generate')
            ->setDescription('Generates a static config file from your .env configuration')
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
        $envFile = base_path('.env');
        $outputConfig = base_path('config/slackhouse.php');

        $generator = new ConfigGenerator($envFile, $outputConfig);
        $generator->generate();

        $output->writeln('Config generated at ' . $outputConfig);
    }
}

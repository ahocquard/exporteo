<?php

declare(strict_types=1);

namespace App\Infrastructure\Delivery\CLI;

use App\Application\ExportProductsToCsvCommand;
use App\Application\ExportProductsToCsvCommandHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TestCommand extends Command
{
    protected static $defaultName = 'app:test';

    protected function configure()
    {
        $this
            ->addOption('client', 'c', InputOption::VALUE_REQUIRED, 'Client id')
            ->addOption('secret', 's', InputOption::VALUE_REQUIRED, 'Secret for the client')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Username')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password of the user')
            ->addOption('uri', null, InputOption::VALUE_REQUIRED, 'Uri of the PIM');
    }

    // TODO: use DI
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new ExportProductsToCsvCommand(
            $input->getOption('client'),
            $input->getOption('secret'),
            $input->getOption('username'),
            $input->getOption('password'),
            $input->getOption('uri')
        );
        $commandHandler = new ExportProductsToCsvCommandHandler();
        $commandHandler->handle($command);
    }
}

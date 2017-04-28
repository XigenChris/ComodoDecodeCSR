<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen\Console;

use Xigen\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
    /**
     * Load a .csr file via an CLI argument
     * @param  Symfony\Component\Console\Input\InputInterface $input
     * @param  Symfony\Component\Console\Output\OutputInterface $output
     * @return bool|void
     */
    public function loadCSR(InputInterface $input, OutputInterface $output)
    {
        $csrFile = $input->getArgument('csr');
        if (!file_exists($csrFile)) {
            $output->writeln('<error>Unable to load '. $csrFile .'</error>');
            $output->writeln('<error>Please check the path and try again</error>');

            exit();
        }

        return file_get_contents($csrFile);
    }
}

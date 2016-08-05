<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Xigen\ComodoDecodeCSR;

class Check extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName("check")
            ->setDescription("Check if a domain will pass the DCV")
            ->addArgument(
                'csr',
                InputArgument::REQUIRED,
                'Location of csr file for this domain'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csrFile = $input->getArgument('csr');
        if (!file_exists($csrFile)) {
            $output->writeln('<error>Unable to load '. $csrFile .'</error>');
            $output->writeln('<error>Please check the path and try again</error>');
            return false;
        }

        $csr = file_get_contents($csrFile);

        $ComodoDecodeCSR = new ComodoDecodeCSR();
        $ComodoDecodeCSR->setCSR($csr);
        $ComodoDecodeCSR->fetchHashes();

        if ($ComodoDecodeCSR->checkInstalled()) {
            $output->writeln('<info>Success!</info>');
            $output->writeln('This domain should pass DCV');

            return true;
        }

        $output->writeln('<error>Fail!</error>');
        $output->writeln('There is something wrong with the validation file');

        return false;
    }
}

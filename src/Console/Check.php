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
            ->setDescription("Check if a domain will pass the DVC")
            ->addArgument(
                'csr',
                InputArgument::REQUIRED,
                'Location of csr file for this domain'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $comodoDecodeCSR = new ComodoDecodeCSR();
        $comodoDecodeCSR->setCSR($this->loadCSR($input, $output));
        $comodoDecodeCSR->fetchHashes();

        if ($comodoDecodeCSR->checkInstalled()) {
            $output->writeln('<info>Success!</info> This domain should pass DVC');

            return 2;
        }

        $output->writeln('<error>Fail!</error> There is something wrong with the validation file');

        return 0;
    }
}

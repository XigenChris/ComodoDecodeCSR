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

class CreateFile extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName("createfile")
            ->setDescription("Creates the file needed for DVC")
            ->addArgument(
                'csr',
                InputArgument::REQUIRED,
                'Location of csr file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csrFile = $input->getArgument('csr');
        if (!file_exists($csrFile)) {
            $output->writeln('<error>Unable to load '. $csrFile .'</error>');
            $output->writeln('<error>Please check the path and try again</error>');

            return 1;
        }

        $csr = file_get_contents($csrFile);

        $comodoDecodeCSR = new ComodoDecodeCSR();
        $comodoDecodeCSR->setCSR($csr);
        $hashes = $comodoDecodeCSR->fetchHashes();

        if (!$hashes) {
            $output->writeln('<error>Fail!</error>');
            $output->writeln('Unable to fetch hashes');

            return 2;
        }

        $output->writeln('<info>Filename:</info> ' . $hashes['md5'] . '.txt');
        $output->writeln('<info>Contents:</info>');
        $output->writeln($comodoDecodeCSR->generateDVC());
        $output->writeln('<info>URL:</info> http://' . $comodoDecodeCSR->getCN() . '/' . $hashes['md5'] . '.txt');

        return 0;
    }
}
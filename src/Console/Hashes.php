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

class Hashes extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName("hashes")
            ->setDescription("Get hashes from a CSR file")
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

        if ($hashes) {
            $output->writeln('<info>MD5</info> ' . $hashes['md5']);
            $output->writeln('<info>SHA1</info> ' . $hashes['sha1']);
            
            return 0;
        }

        $output->writeln('<error>Fail!</error>');
        $output->writeln('Unable to fetch hashes');

        return 2;
    }
}

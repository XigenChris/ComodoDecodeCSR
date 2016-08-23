<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen\Tests\Console;

use Xigen\ComodoDecodeCSR;
use Xigen\Tests\XigenUnit;
use Xigen\Console\Check;
use Symfony\Component\Console\Tester\CommandTester;

class CheckTest extends XigenUnit
{
    protected $ComodoDecodeCSR;

    public function setUp()
    {
        register_shutdown_function(function () {
            if (file_exists('test.csr')) {
                unlink('test.csr');
            }
        });
    }

    public function testNoCSR()
    {
        $command = new Check();
        $tester = new CommandTester($command);

        try {
            $tester->execute([]);
        } catch (\Exception $e) {
            $this->assertEmpty($tester->getDisplay(), "Message wan't empty");
            $this->assertSame($e->getMessage(), 'Not enough arguments (missing: "csr").');
        }
    }

    public function testWithInvalidCSR()
    {
        $command = new Check();
        $tester = new CommandTester($command);

        //Create an empty
        file_put_contents('test.csr', '', FILE_APPEND | LOCK_EX);

        $tester->execute([
            'csr' => 'test.csr'
        ]);

        $this->assertSame($tester->getStatusCode(), (int) 3);
        $this->assertRegExp('/Error!/', $tester->getDisplay());
    }

    public function testWithBadCSR()
    {
        $command = new Check();
        $tester = new CommandTester($command);

        //Create an empty
        file_put_contents('test.csr', ' ' . $this->createFakeCSR(), FILE_APPEND | LOCK_EX);

        $tester->execute([
            'csr' => 'test.csr'
        ]);

        $this->assertSame($tester->getStatusCode(), (int) 3);
        $this->assertRegExp('/Error!/', $tester->getDisplay());
    }

    public function testWithCorrectCSR()
    {
        $command = new Check();
        $tester = new CommandTester($command);
        $tester->execute([
            'csr' => 'certificate/test.csr'
        ]);

        $this->assertSame($tester->getStatusCode(), (int) 0);
        $this->assertRegExp('/Success!/', $tester->getDisplay());
    }
}

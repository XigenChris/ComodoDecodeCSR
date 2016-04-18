<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen\Tests;

use Xigen\ComodoDecodeCSR;

class ComodoDecodeCSRTest extends XigenUnit
{
    protected $ComodoDecodeCSR;
    
    public function setUp()
    {
        $this->ComodoDecodeCSR = new ComodoDecodeCSR();
    }

    public function testSettingCSR()
    {
        //Load the test CSR
        $csr = $this->loadTestCSR();
        $this->ComodoDecodeCSR->setCSR($csr);
        $this->assertSame(
            $csr,
            $this->ComodoDecodeCSR->getCSR(),
            "Unable to set the CSR via ->setCSR()"
        );
    }

    public function testGettingHashes()
    {
        $this->ComodoDecodeCSR->setCSR($this->loadTestCSR());
        $Hashes = $this->ComodoDecodeCSR->fetchHashes();

        $this->assertSame($this->validMD5, $Hashes["md5"], "md5 didn't match the correct value");
        $this->assertSame($this->validMD5, $this->ComodoDecodeCSR->getMD5(), "md5 didn't match the correct value");

        $this->assertSame($this->validSHA1, $Hashes["sha1"], "sha1 didn't match the correct value");
        $this->assertSame($this->validSHA1, $this->ComodoDecodeCSR->getSHA1(), "sha1 didn't match the correct value");
    }

    public function testGettingHashesFromInvalidCSR()
    {
        $Hashes = $this->ComodoDecodeCSR->fetchHashes();

        $this->assertSame($Hashes["md5"], '', "a md5 was set");
        $this->assertSame($Hashes["sha1"], '', "a sha1 was set");
    }

    public function testCheckingInstalled()
    {
        $csr = $this->loadTestCSR();
        $this->ComodoDecodeCSR->setCSR($csr);
        $this->ComodoDecodeCSR->fetchHashes();
        $Installed = $this->ComodoDecodeCSR->checkInstalled();

        $this->assertTrue($Installed);
    }
}

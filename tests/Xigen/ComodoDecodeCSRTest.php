<?php
/**
* @author Chris Hilsdon <chrish@xigen.co.uk>
*/
namespace Xigen\Tests;

use Xigen\ComodoDecodeCSR;

class ComodoDecodeCSRTest extends XigenUnit
{
    private $validMD5 = "244A9E11A76D297F0816A92F477DF543";
    private $validSHA1 = "8FC930D6EDE2844D9DA147F9928178AEFB5B7E89";

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
        $csr = $this->loadTestCSR();
        $this->ComodoDecodeCSR->setCSR($csr);
        $Hashes = $this->ComodoDecodeCSR->getHashes();

        $this->assertSame($this->validMD5, $Hashes["md5"], "md5 didn't match the correct value");
        $this->assertSame($this->validSHA1, $Hashes["sha1"], "sha1 didn't match the correct value");
    }
}

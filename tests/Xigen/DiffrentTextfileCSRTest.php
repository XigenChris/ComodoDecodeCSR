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

class DiffrentTextfileCSRTest extends XigenUnit
{
    protected $ComodoDecodeCSR;
    protected $Hashes;

    public function setUp()
    {
        $this->ComodoDecodeCSR = new ComodoDecodeCSR();
        $this->ComodoDecodeCSR->setCSR($this->loadTestCSR());
        $this->Hashes = $this->ComodoDecodeCSR->fetchHashes();
    }

    private function checkresponse($response)
    {
        return $this->ComodoDecodeCSR->checkDVC($response);
    }

    public function testWithEmptyresponse()
    {
        $response = "";
        $test = $this->checkresponse($response);
        $this->assertFalse($test);
    }

    public function testWithTrailingReturn()
    {
        $response = "\n";
        $test = $this->checkresponse($response);
        $this->assertFalse($test);
    }

    public function testWithJustSH1()
    {
        $response = $this->validSHA1 . "\n";
        $test = $this->checkresponse($response);
        $this->assertFalse($test);
    }

    public function testWithSH1AndComdoText()
    {
        $response = $this->validSHA1 . "\n";
        $response .= "comodoca.com\n";
        $test = $this->checkresponse($response);
        $this->assertTrue($test);
    }

    public function testWithSH1AndComdoTextNoTrailingReturn()
    {
        $response = $this->validSHA1 . "\n";
        $response .= "comodoca.com";
        $test = $this->checkresponse($response);
        $this->assertTrue($test);
    }

    public function testWithSH1AndComdoTextAndNewLine()
    {
        $response = $this->validSHA1 . "\n";
        $response .= "comodoca.com\n\n";
        $test = $this->checkresponse($response);
        $this->assertTrue($test);
    }
}

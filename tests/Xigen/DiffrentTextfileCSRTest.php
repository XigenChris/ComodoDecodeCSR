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

    private function checkResponce($responce)
    {
        return $this->ComodoDecodeCSR->checkDVC($responce);
    }

    public function testWithEmptyResponce()
    {
        $responce = "";
        $test = $this->checkResponce($responce);
        $this->assertFalse($test);
    }

    public function testWithTrailingReturn()
    {
        $responce = "\n";
        $test = $this->checkResponce($responce);
        $this->assertFalse($test);
    }

    public function testWithJustSH1()
    {
        $responce = $this->validSHA1 . "\n";
        $test = $this->checkResponce($responce);
        $this->assertFalse($test);
    }

    public function testWithSH1AndComdoText()
    {
        $responce = $this->validSHA1 . "\n";
        $responce .= "comodoca.com\n";
        $test = $this->checkResponce($responce);
        $this->assertTrue($test);
    }

    public function testWithSH1AndComdoTextNoTrailingReturn()
    {
        $responce = $this->validSHA1 . "\n";
        $responce .= "comodoca.com";
        $test = $this->checkResponce($responce);
        $this->assertTrue($test);
    }
}

<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen\Tests;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use Xigen\ComodoDecodeCSR;

class RedirectTest extends XigenUnit
{
    protected $ComodoDecodeCSR;

    public function setUp()
    {
        $this->ComodoDecodeCSR = new ComodoDecodeCSR();
    }

    public function test301Redirect()
    {
        $stream = Psr7\stream_for('{"data" : "test"}');
        $response = new Response(301, ['Location' => 'https://comododecodecsr.tk'], $stream);

        $test = $this->ComodoDecodeCSR->checkDVC($response);
        $this->assertFalse($test, "Failed to check 301 redirect");
    }

    public function test302Redirect()
    {
        $stream = Psr7\stream_for('{"data" : "test"}');
        $response = new Response(302, ['Location' => 'https://comododecodecsr.tk'], $stream);

        $test = $this->ComodoDecodeCSR->checkDVC($response);
        $this->assertFalse($test, "Failed to check 302 redirect");
    }
}

<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen\Tests;

abstract class XigenUnit extends \PHPUnit_Framework_TestCase
{
    public function loadTestCSR()
    {
        return file_get_contents('certificate/test.csr');
    }
}

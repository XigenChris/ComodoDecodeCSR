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
    public $validMD5 = "98EB197EF83F7A9EB736ED7CEBD413CE";
    public $validSHA1 = "DA9C72B6F6BCB05772BF8543E19D1A41B0210E84";

    public function loadTestCSR()
    {
        return file_get_contents('certificate/test.csr');
    }
}

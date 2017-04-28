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

    public function createFakeCSR()
    {
        $dn = array(
            "countryName" => "NA",
            "stateOrProvinceName" => "NA",
            "localityName" => "NA",
            "organizationName" => "NA",
            "organizationalUnitName" => "NA",
            "commonName" => "httpbin.org",
            "emailAddress" => "NA"
        );

        // Generate a new private (and public) key pair
        $privkey = openssl_pkey_new();

        // Generate a certificate signing request
        return openssl_csr_new($dn, $privkey);
    }

    public function loadTestCSR()
    {
        return file_get_contents('certificate/test.csr');
    }

    public function getOutputLines($display)
    {
        return preg_split("/((\r?\n)|(\r\n?))/", $display);
    }

}

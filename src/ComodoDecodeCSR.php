<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ComodoDecodeCSR
{
    use Traits\GetSetUnset;

    protected $MD5;
    protected $SHA1;
    protected $Endpoint = "https://secure.comodo.net/products/!decodeCSR";
    protected $CSR;
    protected $Form = [
        'responseFormat' => 'N',
        'showErrorCodes' => 'N',
        'showErrorMessages' => 'N',
        'showFieldNames' => 'N',
        'showEmptyFields' => 'N',
        'showCN' => 'N',
        'showAddress' => 'N',
        'showPublicKey' => 'N',
        'showKeySize' => 'N',
        'showSANDNSNames' => 'Y',
        'showCSR' => 'N',
        'showCSRHashes' => 'Y',
        'showSignatureAlgorithm' => 'N',
        'product' => '',
        'countryNameType' => 'TWOCHAR'
    ];
    private $request;

    public function getCN()
    {
        $CSRInfo = $this->decodeCSR();
        return $CSRInfo['subject']['CN'];
    }

    public function setCSR($CSR)
    {
        //TODO Check that this is a valid CSR
        $this->CSR = $CSR;
        $this->Form['csr'] = $CSR;
    }

    public function fetchHashes()
    {
        $client = new Client();

        $this->request = $client->request('POST', $this->getEndpoint(), [
            'form_params' => $this->Form
        ]);

        return $this->processResponse();
    }

    public function checkInstalled()
    {
        $domain = $this->getCN();
        $URL = 'http://' . $domain . "/" . $this->getMD5() . '.txt';

        $client = new Client();

        try {
            $request = $client->request('GET', $URL);
        } catch (ClientException $e) {
            return false;
        }

        $response = "" . $request->getBody();
        return $this->checkDVC($response);
    }

    public function generateDVC()
    {
        $DVC = $this->getSHA1() . "\n";
        $DVC .= "comodoca.com\n";

        return $DVC;
    }

    public function checkDVC($response)
    {
        $DVC = $this->generateDVC();

        //If the response matches the DVC value return true
        if ($response === $DVC) {
            return true;
        }

        //Check if last 2 characters are new lines
        if (substr($response, -2) === "\n\n") {
            $response = substr($response, 0, -2) . "\n";
        }

        //Check if last character is not a new line
        if (substr($response, -1) !== "\n") {
            //Add said new line
            $response = $response . "\n";
        }

        //Check it again
        if ($response === $DVC) {
            return true;
        }

        return false;
    }

    private function decodeCSR()
    {
        $data = openssl_csr_get_public_key($this->getCSR());
        $details = openssl_pkey_get_details($data);
        $key = $details['key'];
        $subject = openssl_csr_get_subject($this->getCSR());

        return array(
            "subject" => $subject,
            "key" => $key
        );
    }

    private function processResponse()
    {
        $response = $this->request->getBody();
        $lines = explode("\n", $response);
        $data = array();
        //Remove the first array as we don't need the SAN and can cause problems
        //with a multi domain SAN
        unset($lines[0]);

        foreach ($lines as $v) {
            if (!empty($v)) {
                $value = explode("=", $v);
                $data[$value[0]] = $value[1];
            }
        }

        $this->setMD5($data["md5"]);
        $this->setSHA1($data["sha1"]);

        return $data ? $data : false;
    }
}

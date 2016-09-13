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
use GuzzleHttp\Psr7\Response;

class ComodoDecodeCSR
{
    use Traits\GetSetUnset;

    protected $MD5;
    protected $SHA1;
    protected $Endpoint = "https://secure.comodo.net/products/!decodeCSR";
    protected $CSR;
    /**
     * An array of warnings that can be show after the test
     * @var array
     */
    protected $warnings = [];
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

    private $forceSSL = false;

    public function getCN()
    {
        $CSRInfo = $this->decodeCSR();
        return $CSRInfo['subject']['CN'];
    }

    public function setCSR($csr)
    {
        $this->CSR = $csr;
        //Check that this is a valid CSR
        $this->decodeCSR();
        $this->Form['csr'] = $csr;
    }

    protected function addWarning($code, $message)
    {
        $this->warnings[] = [
            $code => $message
        ];
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

        try {
            $domain = $this->getCN();
        } catch (\Exception $e) {
            return false;
        }

        $response = $this->fetchDVCFile($domain);
        if ($response == false) {
            return false;
        }

        $check = $this->checkDVC($response);
        if ($check === true) {
            return $check;
        }

        //Try again but this time use https://
        $this->forceSSL = true;

        $response = $this->fetchDVCFile($domain);
        if ($response == false) {
            return false;
        }

        $check = $this->checkDVC($response);
        if ($check === true) {
            //TODO Add a message to say then you will need to select 'HTTPS CSR
            //Hash'
            return $check;
        }

        return false;
    }

    public function generateDVC()
    {
        $DVC = $this->getSHA1() . "\n";
        $DVC .= "comodoca.com\n";

        return $DVC;
    }

    /**
     *
     * @param  GuzzleHttp\Psr7\Response $response
     * @return bool
     */
    public function checkDVC(Response $response)
    {
        $body = $response->getBody() . '';
        $DVC = $this->generateDVC();

        //Check if we received a 301 or 302 redirect
        if ($response->getStatusCode() === 301 || $response->getStatusCode() == 302) {
            return false;
        }

        //If the response matches the DVC value return true
        if ($body === $DVC) {
            return true;
        }

        //Check if last 2 characters are new lines
        if (substr($body, -2) === "\n\n") {
            $body = substr($body, 0, -2) . "\n";
        }

        //Check if last character is not a new line
        if (substr($body, -1) !== "\n") {
            //Add said new line
            $body = $body . "\n";
        }

        var_dump($body, $DVC);

        //Check it again
        if ($body === $DVC) {
            return true;
        }

        return false;
    }

    private function decodeCSR()
    {
        try {
            $data = openssl_csr_get_public_key($this->getCSR());
            $details = openssl_pkey_get_details($data);
            $key = $details['key'];
            $subject = openssl_csr_get_subject($this->getCSR());
        } catch (\Exception $e) {
            throw new Exception("Invalid CSR");
        }

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

    private function fetchDVCFile($domain)
    {
        //We do most of our DVC over http:// unless the site is fully SSL
        $protocol = 'http://';

        if ($this->forceSSL) {
            $protocol = 'https://';
        }

        $url = $protocol . $domain . "/" . $this->getMD5() . '.txt';

        $client = new Client(['allow_redirects' => false, 'verify' => false]);

        try {
            $response = $client->request('GET', $url);
        } catch (ClientException $e) {
            var_dump('te', $e);
            return false;
        }

        return $response;
    }
}

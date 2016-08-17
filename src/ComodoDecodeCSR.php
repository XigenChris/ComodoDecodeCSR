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
    use Traits\ComodoDecodeCSR\Getters;
    use Traits\ComodoDecodeCSR\Setters;

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
        $domain = $this->getCN();
        //We do most of our DVC over http:// unless the site is fully SSL
        $URL = 'http://' . $domain . "/" . $this->getmd5() . '.txt';

        $client = new Client(['allow_redirects' => false, 'verify' => false]);

        try {
            $response = $client->request('GET', $URL);
        } catch (ClientException $e) {
            return false;
        }

        return $this->checkDVC($response);
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
        if ($response->getStatusCode() === 301 || $response->getStatusCode() == 301) {
            $message = 'There is a redirect inplace. Make sure that its not redirecting to https://';
            $this->addWarning(301, $message);

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

        //Check it again
        if ($body === $DVC) {
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

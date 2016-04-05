<?php
/**
* @author Chris Hilsdon <chrish@xigen.co.uk>
*/
namespace Xigen;

use GuzzleHttp\Client;

class ComodoDecodeCSR
{
    use Traits\ComodoDecodeCSR\Getters;
    use Traits\ComodoDecodeCSR\Setters;

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

    public function fetchHashes()
    {
        $client = new Client();

        $this->request = $client->request('POST', $this->getEndpoint(), [
            'form_params' => $this->Form
        ]);

        return $this->processResponce();
    }

    public function checkInstalled()
    {
        $CSRInfo = $this->decodeCSR();
        $domain = $CSRInfo['subject']['CN'];
        $URL = 'http://' . $domain . "/" . $this->getmd5() . '.txt';

        $client = new Client();

        $request = $client->request('GET', $URL);
        $responce = "" . $request->getBody();
        return ($responce === $this->generateDVC());
    }

    public function generateDVC()
    {
        $DVC = $this->getSHA1() . "\n";
        $DVC .= "comodoca.com\n";

        return $DVC;
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

    private function processResponce()
    {
        $responce = $this->request->getBody();
        $lines = explode("\n", $responce);
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

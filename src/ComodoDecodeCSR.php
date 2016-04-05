<?php
/**
* @author Chris Hilsdon <chrish@xigen.co.uk>
*/
namespace Xigen;

use GuzzleHttp\Client;

class ComodoDecodeCSR
{
    protected $md5;
    protected $sha1;
    protected $Endpoint = "https://secure.comodo.net/products/!decodeCSR";
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
    protected $CSR;
    private $request;

    public function setCSR($CSR)
    {
        //TODO Check that this is a valid CSR
        $this->CSR = $CSR;
        $this->Form['csr'] = $CSR;
    }

    public function getCSR()
    {
        return $this->CSR;
    }

    public function getmd5()
    {
        return $this->md5;
    }

    public function getHashes()
    {
        $client = new Client();

        $this->request = $client->request('POST', $this->Endpoint, [
            'form_params' => $this->Form
        ]);

        return $this->processResponce();
    }

    public function checkInstalled()
    {
        $CSRInfo = $this->decodeCSR();
        $Domain = $CSRInfo['subject']['CN'];
        $URL = 'http://' . $Domain . "/" . $this->getmd5() . '.txt';

        $client = new Client();

        $request = $client->request('GET', $URL);
        $responce = "" . $request->getBody();
        return ($responce === $this->generateDVC());
    }

    public function generateDVC()
    {
        $DVC = $this->sha1 . "\n";
        $DVC .= "comodoca.com\n";

        return $DVC;
    }

    private function decodeCSR()
    {
        $cert_data = openssl_csr_get_public_key($this->getCSR());
        $cert_details = openssl_pkey_get_details($cert_data);
        $cert_key = $cert_details['key'];
        $cert_subject = openssl_csr_get_subject($this->getCSR());

        return array(
            "subject" => $cert_subject,
            "key" => $cert_key
        );
    }

    private function processResponce()
    {
        $Responce = $this->request->getBody();
        $lines = explode("\n", $Responce);
        $data = array();

        foreach ($lines as $v) {
            if (!empty($v)) {
                $value = explode("=", $v);
                $data[$value[0]] = $value[1];
            }
        }

        $this->md5 = $data["md5"];
        $this->sha1 = $data["sha1"];

        return $data ? $data : false;
    }
}

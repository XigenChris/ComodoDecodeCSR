<?php

namespace Xigen;

require 'vendor/autoload.php';

$ComodoDecodeCSR = new ComodoDecodeCSR();
//Get the csr as a string
$csr = file_get_contents('certificate/test.csr');
$ComodoDecodeCSR->setCSR($csr);

$Hashes = $ComodoDecodeCSR->getHashes();
var_dump($Hashes);
/*
array(2) {
  'md5' =>
  string(32) "244A9E11A76D297F0816A92F477DF543"
  'sha1' =>
  string(40) "8FC930D6EDE2844D9DA147F9928178AEFB5B7E89"
}
 */

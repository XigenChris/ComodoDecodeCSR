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
  string(32) "98EB197EF83F7A9EB736ED7CEBD413CE"
  'sha1' =>
  string(40) "DA9C72B6F6BCB05772BF8543E19D1A41B0210E84"
}
*/

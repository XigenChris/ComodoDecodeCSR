# ComodoDecodeCSR
A simple PHP library to assist with passing a DVC check by comodo. It uses a API
Endpoint documented [here](https://goo.gl/pZOWhL)
to get the MD5 & SHA1 hashes. It can then check that these are installed on a
domain by requesting http://yourdomain.com/(MD5Hash).txt.

For more infomation about Domain Control Validation read [this](https://goo.gl/7jDJWW)

# Installation
Installation is done via composer:
`composer require xigen/comodo-decode-csr`

# Requirements
Below is a list of requirements. There are unit tests to check compatibility with
the diffrent PHP versions.

- PHP 5.5+ (Tested 5.5, 5.6, 7 & HHVM)
- [Guzzle](https://github.com/guzzle/guzzle) 6.X
- The php-curl extension

# Example Usage
This will use the test CSR within the repo and get the MD5 and SHA1 hashes. Then
it will check that the text file is installed correctly.
```
require 'vendor/autoload.php';

$ComodoDecodeCSR = new ComodoDecodeCSR();

//Get the csr from a file as a string or could just use a string
$csr = file_get_contents('certificate/test.csr');
$ComodoDecodeCSR->setCSR($csr);

$Hashes = $ComodoDecodeCSR->fetchHashes();
$Check = $ComodoDecodeCSR->checkInstalled();
var_dump($Hashes, $Check);
/*
array(2) {
  'md5' =>
  string(32) "98EB197EF83F7A9EB736ED7CEBD413CE"
  'sha1' =>
  string(40) "DA9C72B6F6BCB05772BF8543E19D1A41B0210E84"
}
bool(true)
*/
```

# Licence and Contribution
This source code is released under the GNU General Public License v3. Contributions
are welcome in the form of pull requests. The code is written to PSR-2 standards.
PHPUnit tests have been written and are located in the `tests/` folder.

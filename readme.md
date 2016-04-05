# ComodoDecodeCSR
A simple PHP library to assist with passing a DVC check by comodo. It uses a API
Endpoint documented [here](https://goo.gl/pZOWhL)
to get the MD5 & SHA1 hashes. It can then check that these are installed on a
domain by requesting http://yourdomain.com/<Upper case value of MD5 hash of CSR>.txt.

For more infomation about Domain Control Validation read [this](https://goo.gl/7jDJWW)

# Installation
Installation is done via composer:
`composer require xigen/comodo-decode-csr`

# Requirements
Bewlow is a list of requirements. There are unit tests to check compatibility with the diffrent PHP versions.

- PHP 5.5+ (Tested 5.5, 5.6, 7 & HHVM)
- [Guzzle](https://github.com/guzzle/guzzle) 6.X
- The php-curl extension
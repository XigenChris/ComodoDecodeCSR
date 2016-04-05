<?php

namespace Xigen\Traits\ComodoDecodeCSR;

trait Setters
{
    public function setMD5($MD5)
    {
        return $this->MD5 = $MD5;
    }

    public function setSHA1($SHA1)
    {
        return $this->SHA1 = $SHA1;
    }

    public function setCSR($CSR)
    {
        //TODO Check that this is a valid CSR
        $this->CSR = $CSR;
        $this->Form['csr'] = $CSR;
    }
}

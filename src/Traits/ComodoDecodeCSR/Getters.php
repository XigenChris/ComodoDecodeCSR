<?php

namespace Xigen\Traits\ComodoDecodeCSR;

trait Getters
{
    public function getMD5()
    {
        return $this->MD5;
    }

    public function getSHA1()
    {
        return $this->SHA1;
    }

    public function getEndpoint()
    {
        return $this->Endpoint;
    }

    public function getCSR()
    {
        return $this->CSR;
    }
}

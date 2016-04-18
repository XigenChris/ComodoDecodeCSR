<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

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

    public function getCN()
    {
        $CSRInfo = $this->decodeCSR();
        return $CSRInfo['subject']['CN'];
    }

    public function getCSR()
    {
        return $this->CSR;
    }
}

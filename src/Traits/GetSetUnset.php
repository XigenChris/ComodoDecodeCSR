<?php
/**
 * @author     Chris Hilsdon <chrish@xigen.co.uk>
 * @package    ComodoDecodeCSR
 * @copyright  2016 Xigen
 * @license    GNU General Public License v3
 * @link       https://github.com/XigenChris/ComodoDecodeCSR
 */

namespace Xigen\Traits;

trait GetSetUnset
{
    public function __call($method, $args)
    {
        $data = false;
        switch (substr($method, 0, 3)) {
            case 'get':
                $get = substr($method, 3);
                $data = $this->$get;
                break;
            case 'set':
                $set = substr($method, 3);
                $data = $args[0];
                $this->$set = $data;
                break;
        }

        return $data;
    }
}

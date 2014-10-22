<?php

namespace SURFnet\OATHBundle\Services\OATH;

class Factory
{
    /**
     * Create an instance of an oath service implementation
     *
     * @param string $type
     * @param array $options
     *
     * @return OCRA|HOTP|TOTP
     */
    public function createOATHService($type = 'ocra', $options = array())
    {
        switch ($type) {
            case 'yubiotp':
                $instance = new YubiOTP($options);
                break;
            case "hotp":
                $instance = new HOTP($options);
                break;
            case "totp":
                $instance = new TOTP($options);
                break;
            case "ocra":
            default:
                $instance = new OCRA($options);
                break;
        }
        $instance->init();
        return $instance;
    }
}
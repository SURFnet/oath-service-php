<?php

namespace SURFnet\OATHBundle\Services\OATH;

class Factory
{
    /**
     * Create an instance of a user class implementation
     *
     * @param string $type
     * @param array $options
     *
     * @return OCRA|HOTP|TOTP
     *
     * @throws Exception
     */
    public function createOATHService($type = 'ocra', $options = array())
    {
        switch ($type) {
            case "ocra":
                $instance = new OCRA($options);
                break;
            case "hotp":
                $instance = new HOTP($options);
                break;
            case "totp":
                $instance = new TOTP($options);
                break;
            default:
                throw new \Exception("unknown_type", 404);
        }
        $instance->init();
        return $instance;
    }
}
<?php

namespace SURFnet\OATHBundle\Services\OATH;

class RandomGenerator
{
    /**
     * Borrowed from SimpleSAMLPHP http://simplesamlphp.org/
     */
    public static function generateRandomBytesMTrand($length)
    {

        /* Use mt_rand to generate $length random bytes. */
        $data = '';
        for ($i = 0; $i < $length; $i++) {
            $data .= chr(mt_rand(0, 255));
        }

        return $data;
    }

    /**
     * Borrowed from SimpleSAMLPHP http://simplesamlphp.org/
     */
    public static function generateRandomBytes($length, $fallback = true)
    {
        static $fp = null;

        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        }

        if ($fp === null) {
            if (@file_exists('/dev/urandom')) {
                $fp = @fopen('/dev/urandom', 'rb');
            } else {
                $fp = false;
            }
        }

        if ($fp !== false) {
            /* Read random bytes from /dev/urandom. */
            $data = fread($fp, $length);
            if ($data === false) {
                throw new \Exception('Error reading random data.');
            }
            if (strlen($data) != $length) {
                if ($fallback) {
                    $data = self::generateRandomBytesMTrand($length);
                } else {
                    throw new \Exception(sprintf(
                        'Did not get requested number of bytes from random source. Requested (%d) got (%d)',
                        $length,
                        strlen($data)
                    ));
                }
            }
        } else {
            /* Use mt_rand to generate $length random bytes. */
            $data = self::generateRandomBytesMTrand($length);
        }

        return $data;
    }
}

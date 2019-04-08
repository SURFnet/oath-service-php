<?php

namespace SURFnet\OATHBundle\OATH;

class TOTP extends HOTP
{
    /**
     * Calculate a TOTP response
     *
     * @param string         $secret
     * @param integer        $window    Window in seconds
     * @param integer        $length
     * @param string|boolean $timestamp
     *
     * @return string The response
     */
    public function calculateResponse($secret, $window, $length = 6, $timestamp = false)
    {
        if (!$timestamp && $timestamp !== 0) {
            $timestamp = time();
        }
        $counter = intval($timestamp / $window);

        $hash = $this->generateHash($secret, $counter);
        return $this->truncate($hash, $length);
    }
}

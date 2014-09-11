<?php

namespace SURFnet\OATHBundle\Services\OATH;

class HOTP extends OATHAbstract
{
    /**
     * Validate response using the challenge and optionally the userId and sessionKey
     *
     * @param string $response
     * @param string $challenge
     * @param string $secret
     * @param string $sessionKey
     *
     * @return boolean
     */
    public function validateResponse($response, $challenge, $secret = null, $sessionKey = null)
    {

    }
}
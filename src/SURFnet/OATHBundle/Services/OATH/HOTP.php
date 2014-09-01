<?php

namespace SURFnet\OATHBundle\Services\OATH;

class HOTP extends OATHAbstract
{
    /**
     * Generate the challenge
     *
     * @return string
     */
    public function generateChallenge()
    {

    }

    /**
     * Validate response using the challenge and optionally the userId and sessionKey
     *
     * @param string $response
     * @param string $challenge
     * @param string $userId
     * @param string $sessionKey
     *
     * @return boolean
     */
    public function validateResponse($response, $challenge, $userId = null, $sessionKey = null)
    {

    }
}
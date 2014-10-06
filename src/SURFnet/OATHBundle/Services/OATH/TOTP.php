<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\Services\UserStorage\UserStorageAbstract;
use SURFnet\OATHBundle\OATH\TOTP as OATH_TOTP;

class TOTP extends OATHService
{
    /**
     * Validate response using the
     *
     * @param string                $response
     * @param string                $userId
     * @param UserStorageAbstract   $userStorage
     *
     * @return boolean
     */
    public function validateResponse($response, $userId, UserStorageAbstract $userStorage)
    {
        $secret = $userStorage->getSecret($userId);
        $totp = new OATH_TOTP();
        $totpResponse = $totp->calculateResponse($secret, $this->options['window'], $this->options['length']);
        return ($totpResponse == $response);
    }
}
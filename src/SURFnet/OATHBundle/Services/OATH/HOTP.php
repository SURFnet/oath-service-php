<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\Services\UserStorage\UserStorageAbstract;
use SURFnet\OATHBundle\OATH\HOTP as OATH_HOTP;

class HOTP extends OATHService
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
        $user = $userStorage->getSecretInfo($userId);
        $hotp = new OATH_HOTP();
        $hotpResponse = $hotp->calculateResponse($user['secret'], $user['counter'], $this->options['length']);
        if ($hotpResponse == $response) {
            $userStorage->updateCounter($userId);
            return true;
        }
        return false;
    }
}
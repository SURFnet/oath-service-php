<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\OATH\TOTP as OATH_TOTP;
use SURFnet\OATHBundle\Services\UserStorage\UserStorageInterface;

class TOTP extends OATHService
{
    public function validateResponse(string $response, string $userId, UserStorageInterface $userStorage): bool
    {
        $secret = $userStorage->getSecret($userId);
        $totp = new OATH_TOTP($this->getHash());
        $totpResponse = $totp->calculateResponse($secret, $this->options['window'], $this->options['length']);
        return ($totpResponse == $response);
    }
}

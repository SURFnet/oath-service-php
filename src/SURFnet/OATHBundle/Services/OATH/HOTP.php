<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\OATH\HOTP as OATH_HOTP;
use SURFnet\OATHBundle\Services\UserStorage\UserStorageInterface;

class HOTP extends OATHService
{
    public function validateResponse(string $response, string $userId, UserStorageInterface $userStorage): bool
    {
        $user = $userStorage->getSecretInfo($userId);
        $hotp = new OATH_HOTP($this->getHash());

        $hotpResponse = $hotp->calculateResponse($user['secret'], $user['counter'], $this->options['length']);

        if ($hotpResponse == $response) {
            $userStorage->updateCounter($userId);
            return true;
        }
        return false;
    }
}

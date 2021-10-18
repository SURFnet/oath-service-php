<?php

namespace SURFnet\OATHBundle\Services\Hash;

use RuntimeException;
use SURFnet\OATHBundle\Services\HSM\YubiHSM;

class HSM implements HashInterface
{

    /**
     * @var YubiHSM
     */
    private $hsm;

    public function sha1Hmac($data, $key)
    {
        $secret = json_decode($key);
        if (!$secret) {
            throw new RuntimeException("Expected key to be a json string");
        }

        return $this->hsm->sha1Hmac($secret->aead, $secret->nonce, $data);
    }

    public function setYubiHSM(YubiHSM $hsm)
    {
        $this->hsm = $hsm;
    }
}

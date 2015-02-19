<?php
/**
 * Created by PhpStorm.
 * User: mikko
 * Date: 24/10/14
 * Time: 16:15
 */

namespace SURFnet\OATHBundle\Services\UserStorage;

use SURFnet\OATHBundle\Services\HSM\YubiHSM;

class PDOHSM extends PDO {

    private $hsm;

    // Protect the secret in AEAD
    public function saveSecret($identifier, $secret)
    {
        $secret = $this->hsm->initOath ($secret);
        return parent::saveSecret($identifier, $secret);
    }

    public function setYubiHSM (YubiHSM $hsm) {
        $this->hsm = $hsm;
    }

} 
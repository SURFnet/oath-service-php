<?php

namespace SURFnet\OATHBundle\Services\UserStorage;

class YubiHSM extends UserStorageAbstract
{
    /**
     */
    public function init()
    {
    }

    /**
     * Get the users secret
     *
     * @param string $identifier
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getSecret($identifier)
    {

    }

    /**
     * Get the users secret and counter
     *
     * @param string $identifier
     *
     * @return array
     */
    public function getSecretInfo($identifier)
    {

    }

    /**
     * Save the secret
     *
     * @param string $identifier
     * @param string $secret
     */
    public function saveSecret($identifier, $secret)
    {

    }

    /**
     * Update the user's counter (if possible, used for HOTP validation)
     *
     * @param string $identifier
     */
    public function updateCounter($identifier)
    {

    }

    /**
     * Delete the secret
     *
     * @param string $identifier
     *
     * @throws \Exception
     */
    public function deleteSecret($identifier)
    {
    }
}
<?php

namespace SURFnet\OATHBundle\Services\UserStorage;

interface UserStorageInterface
{
    /**
     * An initializer that will be called directly after instantiating
     * the class. Derived classes can override this to perform
     * initialization of the OATH class.
     *
     * Note: this method is not abstract since not every derived class
     * will want to implement this.
     */
    public function init();

    /**
     * Get the users secret
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getSecret($identifier);

    /**
     * Get the users secret and counter
     *
     * @param string $identifier
     *
     * @return array
     */
    public function getSecretInfo($identifier);

    /**
     * Save the secret
     *
     * @param string $identifier
     * @param string $secret
     */
    public function saveSecret($identifier, $secret);

    /**
     * Update the user's counter (if possible, used for HOTP validation)
     *
     * @param string $identifier
     */
    public function updateCounter($identifier);

    /**
     * Delete the secret
     *
     * @param string $identifier
     */
    public function deleteSecret($identifier);
}

<?php

namespace SURFnet\OATHBundle\Services\UserStorage\Encryption;

/**
 * Dummy encryption class, returns the data as is.
 * 
 * @author peter
 */
class Dummy implements UserEncryptionInterface
{
    /**
     * Construct an encryption instance.
     *
     * @param $config The configuration that a specific configuration class may use.
     */
    public function __construct($config)
    {
    }
    
    /**
     * Encrypts the given data. 
     *
     * @param String $data Data to encrypt.
     *
     * @return encrypted data
     */
    public function encrypt($data)
    {
        return $data;
    }
    
    /**
      * Decrypts the given data.
     *
     * @param String $data Data to decrypt.
     *
     * @return decrypted data
     */
    public function decrypt($data)
    {
        return $data;
    }
}

<?php

namespace SURFnet\OATHBundle\Services\UserStorage\Encryption;

/**
 * Class for encrypting/decrypting the user secret with openssl.
 *
 * @author peter
 */
class Openssl implements UserEncryptionInterface
{
    private $_method;
    private $_key;

    /**
     * Construct an encryption instance.
     *
     * @param $config The configuration that a specific configuration class may use.
     */
    public function __construct($config)
    {
        $this->_method = $config['method'];
        if (mb_strlen($config['key'], '8bit') !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        $this->_key = $config['key'];
    }

    /**
     * Encrypts the given data.
     *
     * @param String $data Data to encrypt.
     *
     * @return string encrypted data
     */
    public function encrypt($data)
    {
        $ivsize = openssl_cipher_iv_length($this->_method);
        $iv = openssl_random_pseudo_bytes($ivsize);

        $ciphertext = openssl_encrypt(
            $data,
            $this->_method,
            $this->_key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($ciphertext . '::' . $iv);
    }

    /**
     * Decrypts the given data.
     *
     * @param String $data Data to decrypt.
     *
     * @return string decrypted data
     */
    public function decrypt($data)
    {
        $ivsize = openssl_cipher_iv_length($this->_method);
        list($ciphertext, $iv) = explode('::', base64_decode($data), 2);

        return openssl_decrypt(
            $ciphertext,
            $this->_method,
            $this->_key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}

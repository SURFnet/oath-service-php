<?php

namespace SURFnet\OATHBundle\Services\UserStorage\Encryption;

use Exception;

/**
 * Class for encrypting/decrypting the user secret with openssl.
 *
 * @author peter
 */
class Openssl implements UserEncryptionInterface
{
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $key;

    /**
     * Construct an encryption instance.
     *
     * @param $config array The configuration that a specific configuration class may use.
     * @throws Exception
     */
    public function __construct($config)
    {
        $this->method = $config['method'];
        if (mb_strlen($config['key'], '8bit') !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        $this->key = $config['key'];
    }

    /**
     * Encrypts the given data.
     *
     * @param string $data Data to encrypt.
     *
     * @return string encrypted data
     */
    public function encrypt($data)
    {
        $ivsize = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivsize);

        $ciphertext = openssl_encrypt(
            $data,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($ciphertext . '::' . $iv);
    }

    /**
     * Decrypts the given data.
     *
     * @param string $data Data to decrypt.
     *
     * @return string decrypted data
     */
    public function decrypt($data)
    {
        list($ciphertext, $iv) = explode('::', base64_decode($data), 2);

        return openssl_decrypt(
            $ciphertext,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}

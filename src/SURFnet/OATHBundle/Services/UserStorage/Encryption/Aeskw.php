<?php 

namespace SURFnet\OATHBundle\Services\UserStorage\Encryption;

use AESKW\AESKW256;

/**
 * Class for wrapping/unwrapping c.q. encrypting/decrypting the user secret with an AES Key Wrap algorithm.
 * Uses rfc3394 key wrapping as implemented in https://github.com/sop/aes-kw
 * 
 * @author joost
 */
class Aeskw implements UserEncryptionInterface
{
    private $_algo;
    private $_kek; // Key Encryption Key

    /**
     * Construct an encryption instance.
     *
     * @param $config The configuration that a specific configuration class may use.
     */
    public function __construct($config)
    {
        $this->_algo = new AESKW256();
        $this->_kek = $config['kek'];
    }
    
    /**
     * Wraps/Encrypts the given key.
     *
     * @param String $key Key to wrap/encrypt.
     *
     * @return wrapped key
     */
    public function encrypt($key)
    {
        return bin2hex($this->_algo->wrap($key, $this->_kek));
    }
    
    /**
      * Unwraps/Decrypts the given key.
     *
     * @param String $data Key to unwrap/decrypt.
     *
     * @return unwrapped key
     */
    public function decrypt($key)
    {
        return $this->_algo->unwrap(hex2bin($key), $this->_kek);
    }
}

<?php 

namespace SURFnet\OATHBundle\Services\UserStorage\Encryption;

/**
 * Class for encrypting/decrypting the user secret with mcrypt.
 * 
 * @author peter
 */
class Mcrypt implements UserEncryptionInterface
{
    private $_cipher;
    private $_mode;
    private $_key;
    private $_iv;
    
    /**
     * Construct an encryption instance.
     *
     * @param $config The configuration that a specific configuration class may use.
     */
    public function __construct($config)
    {
        $this->_cipher = $config['cipher'];
        $this->_mode = $config['mode'];
        $this->_key = $config['key'];
        $this->_iv = $config['iv'];
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
        return bin2hex(mcrypt_encrypt($this->_cipher, $this->_key, $data, $this->_mode, $this->_iv));
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
        return rtrim(mcrypt_decrypt($this->_cipher, $this->_key, pack("H*", $data), $this->_mode, $this->_iv), "\0");        
    }
}

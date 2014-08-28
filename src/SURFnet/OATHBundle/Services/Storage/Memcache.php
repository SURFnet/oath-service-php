<?php

namespace SURFnet\OATHBundle\Services\Storage;

use Symfony\Component\Config\Definition\Exception\Exception;

class Memcache extends StorageAbstract
{
    /**
     * The memcache instance.
     * @var Memcache
     */
    protected $_memcache = NULL;

    /**
     * The default configuration
     */
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT =  11211;

    /**
     * Get the prefix to use for all keys in memcache.
     * @return String the prefix
     */
    protected function _getKeyPrefix()
    {
        if (isset($this->_options["prefix"])) {
            return $this->_options["prefix"];
        }
        return "";
    }

    /**
     * Initialize the storage by setting up the memcache instance.
     * It's not necessary to call this function, the storage factory
     * will take care of that.
     */
    public function init()
    {
        parent::init();

        $this->_memcache = new \Memcache();

        if (!isset($this->_options["servers"])) {
            $this->_memcache->addServer(self::DEFAULT_HOST, self::DEFAULT_PORT);
        } else {
            foreach ($this->_options['servers'] as $server) {
                if (!array_key_exists('port', $server)) {
                    $server['port'] = self::DEFAULT_PORT;
                }
                if (!array_key_exists('host', $server)) {
                    $server['host'] = self::DEFAULT_HOST;
                }

                $this->_memcache->addServer($server['host'], $server['port']);
            }
        }
    }

    /**
     * Store a value using key as identifier (overwrites value if key already exists).
     *
     * @param string  $key
     * @param string  $value
     * @param integer $expire
     *
     * @return \SURFnet\OATHBundle\Entity\Storage
     */
    public function storeValue($key, $value, $expire = 0)
    {
        $key = $this->_getKeyPrefix().$key;
        $this->_memcache->set($key, $value, 0, $expire);
        return $this->returnStorage($key, $value);
    }

    /**
     * Get the value from the storage identified by the key.
     *
     * @param string $key
     *
     * @return \SURFnet\OATHBundle\Entity\Storage
     *
     * @throws \Exception
     */
    public function getValue($key)
    {
        $key = $this->_getKeyPrefix().$key;
        $value = $this->_memcache->get($key);
        if ($value === false) {
            throw new Exception('key_not_found', 404);
        }
        return $this->returnStorage($key, $value);
    }

    /**
     * Delete the value from the storage identified by the key.
     *
     * @param string $key
     */
    public function deleteValue($key)
    {
        $key = $this->_getKeyPrefix().$key;
        $this->_memcache->delete($key);
    }
}
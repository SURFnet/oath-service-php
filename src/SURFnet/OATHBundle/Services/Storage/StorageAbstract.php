<?php

namespace SURFnet\OATHBundle\Services\Storage;

use SURFnet\OATHBundle\Entity\Storage;

abstract class StorageAbstract
{
    /**
     * The options for the storage. Derived classes can access this
     * to retrieve options configured for the state storage.
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     * Should not be called directly, use the factory to construct
     * a storage instance of a certain type.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * An initializer that will be called directly after instantiating
     * the storage. Derived classes can override this to perform
     * initialization of the storage.
     *
     * Note: this method is not abstract since not every derived class
     * will want to implement this.
     */
    public function init()
    {

    }

    /**
     * Format the return
     *
     * @param string $key
     * @param string $value
     * @return \SURFnet\OATHBundle\Entity\Storage
     */
    protected function returnStorage($key, $value)
    {
        $storage = new Storage();
        $storage->key = $key;
        $storage->value = $value;
        return $storage;
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
    public abstract function storeValue($key, $value, $expire = 0);

    /**
     * Get the value from the storage identified by the key.
     *
     * @param string $key
     *
     * @return \SURFnet\OATHBundle\Entity\Storage
     */
    public abstract function getValue($key);

    /**
     * Delete the value from the storage identified by the key.
     *
     * @param string $key
     */
    public abstract function deleteValue($key);
}
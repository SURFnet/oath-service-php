<?php

namespace SURFnet\OATHBundle\Services\Storage;

use SURFnet\OATHBundle\Entity\Storage;

class File extends StorageAbstract
{
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
        $storage = new Storage();
        $storage->key = $key;
        $storage->value = $value;
        $storage->createdAt = time();
        $storage->expire = $expire;

        $filename = $this->_getFilename($key);
        file_put_contents($filename, serialize($storage));

        return $this->returnStorage($storage);
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
        $filename = $this->_getFilename($key);
        if (file_exists($filename)) {
            $storage = unserialize(file_get_contents($filename));
            if ($storage->expire!=0) {
                // This data is time-limited. If it's too old we discard it.
                if (time()-$storage->createdAt > $storage->expire) {
                    $this->deleteValue($key);
                    throw new \Exception('value_expired', 400);
                }
            }
            return $this->returnStorage($storage);
        }
        throw new \Exception('key_not_found', 404);
    }

    /**
     * Delete the value from the storage identified by the key.
     *
     * @param string $key
     *
     * @throws \Exception
     */
    public function deleteValue($key)
    {
        $filename = $this->_getFilename($key);
        if (file_exists($filename)) {
            unlink($filename);
        } else {
            throw new \Exception('key_not_found', 404);
        }
    }

    /**
     * Determine the name of a temporary file to hold the contents of $key
     *
     * @param string $key The key for which to store data.
     *
     * @return string The filename
     */
    protected function _getFilename($key)
    {
        return $this->options['file_path'].$this->options['file_prefix'].'_'.strtr(base64_encode($key), '+/', '-_');
    }

    /**
     * Prepare the storage object for returning
     *
     * @param \SURFnet\OATHBundle\Entity\Storage $storage
     *
     * @return \SURFnet\OATHBundle\Entity\Storage
     */
    protected function returnStorage(Storage $storage)
    {
        unset($storage->createdAt, $storage->expire);
        return $storage;
    }
}
<?php

namespace SURFnet\OATHBundle\Services\Storage;

class Factory
{
    /**
     * Create an instance of a storage class implementation
     *
     * @param string $type
     * @param array $options
     *
     * @return File|Memcache|Pdo
     *
     * @throws Exception
     */
    public function createStorage($type = 'file', $options = array())
    {
        switch ($type) {
            case "file":
                $instance = new File($options);
                break;
            case "memcache":
                $instance = new Memcache($options);
                break;
            case "pdo":
                $instance = new Pdo($options);
                break;
            default:
                if (!isset($type)) {
                    throw new Exception('Class name not set');
                } elseif (!class_exists($type)) {
                    throw new Exception('Class not found: ' . var_export($type, TRUE));
                } elseif (!is_subclass_of($type, 'Tiqr_StateStorage_Abstract')) {
                    throw new Exception('Class ' . $type . ' not subclass of Tiqr_StateStorage_Abstract');
                }
                $instance = new $type($options);
        }
        $instance->init();
        return $instance;
    }
}
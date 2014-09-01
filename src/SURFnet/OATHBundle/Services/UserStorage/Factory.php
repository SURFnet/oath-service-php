<?php

namespace SURFnet\OATHBundle\Services\UserStorage;

class Factory
{
    /**
     * Create an instance of an oath service implementation
     *
     * @param string $type
     * @param array $options
     *
     * @return PDO
     */
    public function createUserStorage($type, $options = array())
    {
        switch ($type) {
            case "pdo":
            default:
                $instance = new PDO($options);
                break;
        }
        $instance->init();
        return $instance;
    }
}
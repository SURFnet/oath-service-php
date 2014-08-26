<?php

namespace SURFnet\OATHBundle\Entity;

class Storage
{
    /**
     * The key to identify the value that is stored
     * @var string
     */
    public $key;

    /**
     * The value that is stored
     * @var string
     */
    public $value;

    /**
     * Number of seconds to expire the value (default 0, no expiration)
     * @var integer
     */
    public $expire;

    /**
     * Timestamp when this storage entry is created, used to calculate expiration
     * @var integer
     */
    public $createdAt;
}
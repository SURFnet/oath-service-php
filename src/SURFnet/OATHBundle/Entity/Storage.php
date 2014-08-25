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
}
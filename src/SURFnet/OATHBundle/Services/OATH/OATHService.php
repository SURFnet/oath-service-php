<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\Services\Hash\HashInterface;

class OATHService
{

    private $hash;

    /**
     * The options for the OATH. Derived classes can access this
     * to retrieve options configured.
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     * Should not be called directly, use the factory to construct
     * a OATH instance of a certain type.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * An initializer that will be called directly after instantiating
     * the class. Derived classes can override this to perform
     * initialization of the OATH class.
     *
     * Note: this method is not abstract since not every derived class
     * will want to implement this.
     */
    public function init ()
    {

    }

    public function setHash (HashInterface $hash) {
        $this->hash = $hash;
    }

    public function getHash () {
        return $this->hash;
    }
}
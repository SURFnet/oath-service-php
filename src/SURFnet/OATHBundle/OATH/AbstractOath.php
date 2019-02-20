<?php
namespace SURFnet\OATHBundle\OATH;

use \SURFnet\OATHBundle\Services\Hash\HashInterface;

abstract class AbstractOath
{

    private $hash;

    public function __construct(HashInterface $hash)
    {
        $this->hash = $hash;
    }

    public function setHash(HashInterface $hash)
    {
        $this->hash = $hash;
    }

    public function getHash()
    {
        return $this->hash;
    }
}

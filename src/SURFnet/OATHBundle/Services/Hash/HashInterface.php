<?php
namespace SURFnet\OATHBundle\Services\Hash;

interface HashInterface
{
    public function sha1Hmac($data, $key);
}

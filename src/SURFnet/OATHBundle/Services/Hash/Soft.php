<?php

namespace SURFnet\OATHBundle\Services\Hash;

class Soft implements HashInterface
{

    public function sha1Hmac($data, $key)
    {
        return hash_hmac('sha1', $data, $key);
    }
}

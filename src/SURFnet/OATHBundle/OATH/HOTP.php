<?php

namespace SURFnet\OATHBundle\OATH;

class HOTP extends AbstractOath
{
    /**
     * Calculate a HOTP response
     *
     * @param string  $secret
     * @param string  $counter
     * @param integer $length
     *
     * @return string The response
     */
    public function calculateResponse($secret, $counter, $length = 6)
    {
        $hash = $this->generateHash($secret, $counter);
        return $this->truncate($hash, $length);
    }
    
    /**
     * Compute a hash
     *
     * @param string $secret
     * @param string $counter
     *
     * @return string hash
     */
    protected function generateHash($secret, $counter)
    {
         // Counter
         //the counter value can be more than one byte long, so we need to go multiple times
         $curCounter = array(0,0,0,0,0,0,0,0);
        for ($i=7; $i>=0; $i--) {
            $curCounter[$i] = pack('C*', $counter);
            $counter = $counter >> 8;
        }
         $binCounter = implode($curCounter);
         // Pad to 8 chars
        if (strlen($binCounter) < 8) {
            $binCounter = str_repeat(chr(0), 8 - strlen($binCounter)) . $binCounter;
        }
     
         // HMAC
         $hash = $this->getHash()->sha1Hmac($binCounter, $secret);
         return $hash;
    }
 
    /**
     * Truncate a response to a certain length.
     *
     * @param string  $hash
     * @param integer $length
     *
     * @return string a truncated response
     */
    protected function truncate($hash, $length = 6)
    {
        $hmacResult = [];
         // Convert to dec
        foreach (str_split($hash, 2) as $hex) {
            $hmacResult[]=hexdec($hex);
        }
     
         // Find offset
         $offset = $hmacResult[19] & 0xf;
     
         // Algorithm from RFC
         return
         (
             (($hmacResult[$offset+0] & 0x7f) << 24 ) |
             (($hmacResult[$offset+1] & 0xff) << 16 ) |
             (($hmacResult[$offset+2] & 0xff) << 8 ) |
             ($hmacResult[$offset+3] & 0xff)
         ) % pow(10, $length);
    }
}

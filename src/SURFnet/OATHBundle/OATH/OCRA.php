<?php
/**
 * This file is part of the ocra-implementations package.
 *
 * More information: https://github.com/SURFnet/ocra-implementations/
 *
 * @author Ivo Jansch <ivo@egeniq.com>
 *
 * @license See the LICENSE file in the source distribution
 */

namespace SURFnet\OATHBundle\OATH;

use Exception;
use SURFnet\OATHBundle\Services\Hash\HSM;

/**
 * This a PHP port of the example implementation of the
 * OATH OCRA algorithm.
 * Visit www.openauthentication.org for more information.
 *
 * @author Johan Rydell, PortWise (original Java)
 * @author Ivo Jansch, Egeniq (PHP port)
 */
class OCRA extends AbstractOath
{

    /**
     * This method converts HEX string to Byte[]
     *
     * @param string hex   the HEX string
     *
     * @return string a string with raw bytes
     */
    private function hexStr2Bytes($hex)
    {
        return pack("H*", $hex);
    }


    /**
     * This method generates an OCRA HOTP value for the given
     * set of parameters.
     *
     * @param $ocraSuite    string the OCRA Suite
     * @param $key          string the shared secret, HEX encoded
     * @param $counter      int the counter that changes
     *                     on a per use basis,
     *                     HEX encoded
     * @param $question     string the challenge question, HEX encoded
     * @param $password     string a password that can be used,
     *                     HEX encoded
     * @param $sessionInformation string
     *                     Static information that identifies the
     *                     current session, Hex encoded
     * @param $timeStamp    into value that reflects a time
     *
     * @return string A numeric String in base 10 that includes
     * {@link truncationDigits} digits
     *
     * @SuppressWarnings(PMD.CyclomaticComplexity)
     * @SuppressWarnings(PMD.NPathComplexity)
     * @SuppressWarnings(PMD.ExcessiveMethodLength)
     */
    public function generateOCRA(
        $ocraSuite,
        $key,
        $counter,
        $question,
        $password,
        $sessionInformation,
        $timeStamp
    ) {
        $crypto = "";
        $ocraSuiteLength = strlen($ocraSuite);
        $counterLength = 0;
        $questionLength = 0;
        $passwordLength = 0;

        $sessionInformationLength = 0;
        $timeStampLength = 0;

        // How many digits should we return
        $components = explode(":", $ocraSuite);
        $cryptoFunction = $components[1];
        $dataInput = strtolower($components[2]); // lower here so we can do case insensitive comparisons
        
        if (stripos($cryptoFunction, "sha1")!==false) {
            $crypto = "sha1";
        }
        if (stripos($cryptoFunction, "sha256")!==false) {
            $crypto = "sha256";
        }
        if (stripos($cryptoFunction, "sha512")!==false) {
            $crypto = "sha512";
        }
        
        $codeDigits = substr($cryptoFunction, strrpos($cryptoFunction, "-")+1);
                
        // The size of the byte array message to be encrypted
        // Counter
        if ($dataInput[0] === "c") {
            // Fix the length of the HEX string
            while (strlen($counter) < 16) {
                $counter = "0" . $counter;
            }
            $counterLength=8;
        }
        // Question
        if ($dataInput[0] === "q"
            || stripos($dataInput, "-q")!==false
        ) {
            while (strlen($question) < 256) {
                $question = $question . "0";
            }
            $questionLength=128;
        }

        // Password
        if (stripos($dataInput, "psha1")!==false) {
            while (strlen($password) < 40) {
                $password = "0" . $password;
            }
            $passwordLength=20;
        }
    
        if (stripos($dataInput, "psha256")!==false) {
            while (strlen($password) < 64) {
                $password = "0" . $password;
            }
            $passwordLength=32;
        }
        
        if (stripos($dataInput, "psha512")!==false) {
            while (strlen($password) < 128) {
                $password = "0" . $password;
            }
            $passwordLength=64;
        }
        
        // sessionInformation
        if (stripos($dataInput, "s064") !==false) {
            while (strlen($sessionInformation) < 128) {
                $sessionInformation = "0" . $sessionInformation;
            }

            $sessionInformationLength=64;
        } elseif (stripos($dataInput, "s128") !==false) {
            while (strlen($sessionInformation) < 256) {
                $sessionInformation = "0" . $sessionInformation;
            }
        
            $sessionInformationLength=128;
        } elseif (stripos($dataInput, "s256") !==false) {
            while (strlen($sessionInformation) < 512) {
                $sessionInformation = "0" . $sessionInformation;
            }
        
            $sessionInformationLength=256;
        } elseif (stripos($dataInput, "s512") !==false) {
            while (strlen($sessionInformation) < 128) {
                $sessionInformation = "0" . $sessionInformation;
            }
        
            $sessionInformationLength=64;
        } elseif (stripos($dataInput, "s") !== false) {
            // deviation from spec. Officially 's' without a length indicator is not in the reference implementation.
            // RFC is ambigious. However we have supported this in Tiqr since day 1, so we continue to support it.
            while (strlen($sessionInformation) < 128) {
                $sessionInformation = "0" . $sessionInformation;
            }
            
            $sessionInformationLength=64;
        }
        
        
             
        // TimeStamp
        if ($dataInput[0] === "t"
            || stripos($dataInput, "-t") !== false
        ) {
            while (strlen($timeStamp) < 16) {
                $timeStamp = "0" . $timeStamp;
            }
            $timeStampLength=8;
        }

        // Put the bytes of "ocraSuite" parameters into the message
        
        $msg = array_fill(0, $ocraSuiteLength+$counterLength+$questionLength+$passwordLength+$sessionInformationLength+$timeStampLength+1, 0);
                
        for ($i=0; $i<strlen($ocraSuite); $i++) {
            $msg[$i] = $ocraSuite[$i];
        }
        
        // Delimiter
        $msg[strlen($ocraSuite)] = $this->hexStr2Bytes("0");

        // Put the bytes of "Counter" to the message
        // Input is HEX encoded
        if ($counterLength > 0) {
            $bArray = $this->hexStr2Bytes($counter);
            for ($i=0; $i<strlen($bArray); $i++) {
                $msg [$i + $ocraSuiteLength + 1] = $bArray[$i];
            }
        }


        // Put the bytes of "question" to the message
        // Input is text encoded
        if ($questionLength > 0) {
            $bArray = $this->hexStr2Bytes($question);
            for ($i=0; $i<strlen($bArray); $i++) {
                $msg [$i + $ocraSuiteLength + 1 + $counterLength] = $bArray[$i];
            }
        }

        // Put the bytes of "password" to the message
        // Input is HEX encoded
        if ($passwordLength > 0) {
            $bArray = $this->hexStr2Bytes($password);
            for ($i=0; $i<strlen($bArray); $i++) {
                $msg [$i + $ocraSuiteLength + 1 + $counterLength + $questionLength] = $bArray[$i];
            }
        }

        // Put the bytes of "sessionInformation" to the message
        // Input is text encoded
        if ($sessionInformationLength > 0) {
            $bArray = $this->hexStr2Bytes($sessionInformation);
            for ($i=0; $i<strlen($bArray); $i++) {
                $msg [$i + $ocraSuiteLength + 1 + $counterLength + $questionLength + $passwordLength] = $bArray[$i];
            }
        }

        // Put the bytes of "time" to the message
        // Input is text value of minutes
        if ($timeStampLength > 0) {
            $bArray = $this->hexStr2Bytes($timeStamp);
            for ($i=0; $i<strlen($bArray); $i++) {
                $msg [$i + $ocraSuiteLength + 1 + $counterLength + $questionLength + $passwordLength + $sessionInformationLength] = $bArray[$i];
            }
        }

        $msg = implode("", $msg);

        if (($this->getHash() instanceof HSM)) {
            if ($cryptoFunction !== "sha1") {
                throw new Exception('Only SHA1 HMAC is supported using YubiHSM', 500);
            }
            $hash = $this->getHash()->sha1Hmac($msg, $key);
        } else {
            $byteKey = $this->hexStr2Bytes($key);
            $hash = hash_hmac($crypto, $msg, $byteKey);
        }

        return $this->oathTruncate($hash, $codeDigits);
    }

    /**
     * Truncate a result to a certain length
     */
    private function oathTruncate($hash, $length = 6)
    {
        $hmacResult = [];
        // Convert to dec
        foreach (str_split($hash, 2) as $hex) {
            $hmacResult[]=hexdec($hex);
        }
    
        // Find offset
        $offset = $hmacResult[count($hmacResult) - 1] & 0xf;
    
        $v = strval(
            (($hmacResult[$offset+0] & 0x7f) << 24 ) |
            (($hmacResult[$offset+1] & 0xff) << 16 ) |
            (($hmacResult[$offset+2] & 0xff) << 8 ) |
            ($hmacResult[$offset+3] & 0xff)
        );
        
        $v = substr($v, strlen($v) - $length);
        return $v;
    }
}

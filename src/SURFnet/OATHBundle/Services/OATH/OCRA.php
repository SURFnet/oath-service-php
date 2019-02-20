<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\Services\OATH\RandomGenerator;
use SURFnet\OATHBundle\OATH\OCRA as OATH_OCRA;

/**
 * @SuppressWarnings(PMD.TooManyFields)
 */
class OCRA extends OATHService
{
    private $OCRASuite = null;

    private $OCRAVersion = null;

    private $CryptoFunctionType = null;
    private $CryptoFunctionHash = null;
    private $CryptoFunctionHashLength = null;
    private $CryptoFunctionTruncation = null;

    private $C = false;
    private $Q = false;
    private $QType = 'N';
    private $QLength = 8;

    private $P = false;
    private $PType = 'SHA1';
    private $PLength = 20;

    private $S = false;
    private $SLength = 64;

    private $T = false;
    private $TLength = 60; // 1M
    private $TPeriods = array('H' => 3600, 'M' => 60, 'S' => 1);

    private $supportedHashFunctions = array('SHA1' => 20, 'SHA256' => 32, 'SHA512' => 64);

    /**
     * An initializer that will be called directly after instantiating
     * the class.
     */
    public function init()
    {
        $this->parseOCRASuite($this->options['suite']);
    }

    /**
     * Inspired by https://github.com/bdauvergne/python-oath
     *
     * @SuppressWarnings(PMD.CyclomaticComplexity)
     * @SuppressWarnings(PMD.NPathComplexity)
     * @SuppressWarnings(PMD.ExcessiveMethodLength)
     */
    private function parseOCRASuite($ocraSuite)
    {
        if (!is_string($ocraSuite)) {
            throw new \Exception('OCRASuite not in string format: ' . var_export($ocraSuite, true));
        }

        $ocraSuite = strtoupper($ocraSuite);
        $this->OCRASuite = $ocraSuite;

        $s = explode(':', $ocraSuite);
        if (count($s) != 3) {
            throw new \Exception('Invalid OCRASuite format: ' . var_export($ocraSuite, true));
        }

        $algo = explode('-', $s[0]);
        if (count($algo) != 2) {
            throw new \Exception('Invalid OCRA version: ' . var_export($s[0], true));
        }

        if ($algo[0] !== 'OCRA') {
            throw new \Exception('Unsupported OCRA algorithm: ' . var_export($algo[0], true));
        }

        if ($algo[1] !== '1') {
            throw new \Exception('Unsupported OCRA version: ' . var_export($algo[1], true));
        }
        $this->OCRAVersion = $algo[1];

        $cf = explode('-', $s[1]);
        if (count($cf) != 3) {
            throw new \Exception('Invalid OCRA suite crypto function: ' . var_export($s[1], true));
        }

        if ($cf[0] !== 'HOTP') {
            throw new \Exception('Unsupported OCRA suite crypto function: ' . var_export($cf[0], true));
        }
        $this->CryptoFunctionType = $cf[0];

        if (!array_key_exists($cf[1], $this->supportedHashFunctions)) {
            throw new \Exception('Unsupported hash function in OCRA suite crypto function: ' . var_export($cf[1], true));
        }
        $this->CryptoFunctionHash = $cf[1];
        $this->CryptoFunctionHashLength = $this->supportedHashFunctions[$cf[1]];

        if (!preg_match('/^\d+$/', $cf[2]) || (($cf[2] < 4 || $cf[2] > 10) && $cf[2] != 0)) {
            throw new \Exception('Invalid OCRA suite crypto function truncation length: ' . var_export($cf[2], true));
        }
        $this->CryptoFunctionTruncation = intval($cf[2]);

        $di = explode('-', $s[2]);
        if (count($cf) == 0) {
            throw new \Exception('Invalid OCRA suite data input: ' . var_export($s[2], true));
        }

        $dataInput = array();
        foreach ($di as $elem) {
            $letter = $elem[0];
            if (array_key_exists($letter, $dataInput)) {
                throw new \Exception('Duplicate field in OCRA suite data input: ' . var_export($elem, true));
            }
            $dataInput[$letter] = 1;

            if ($letter === 'C' && strlen($elem) == 1) {
                $this->C = true;
            } elseif ($letter === 'Q') {
                if (strlen($elem) == 1) {
                    $this->Q = true;
                } elseif (preg_match('/^Q([AHN])(\d+)$/', $elem, $match)) {
                    $qLen = intval($match[2]);
                    if ($qLen < 4 || $qLen > 64) {
                        throw new \Exception('Invalid OCRA suite data input question length: ' . var_export($qLen, true));
                    }
                    $this->Q = true;
                    $this->QType = $match[1];
                    $this->QLength = $qLen;
                } else {
                    throw new \Exception('Invalid OCRA suite data input question: ' . var_export($elem, true));
                }
            } elseif ($letter === 'P') {
                if (strlen($elem) == 1) {
                    $this->P = true;
                } else {
                    $pAlgo = substr($elem, 1);
                    if (!array_key_exists($pAlgo, $this->supportedHashFunctions)) {
                        throw new \Exception('Unsupported OCRA suite PIN hash function: ' . var_export($elem, true));
                    }
                    $this->P = true;
                    $this->PType = $pAlgo;
                    $this->PLength = $this->supportedHashFunctions[$pAlgo];
                }
            } elseif ($letter === 'S') {
                if (strlen($elem) == 1) {
                    $this->S = true;
                } elseif (preg_match('/^S(\d+)$/', $elem, $match)) {
                    $sLen = intval($match[1]);
                    if ($sLen <= 0 || $sLen > 512) {
                        throw new \Exception('Invalid OCRA suite data input session information length: ' . var_export($sLen, true));
                    }

                    $this->S = true;
                    $this->SLength = $sLen;
                } else {
                    throw new \Exception('Invalid OCRA suite data input session information length: ' . var_export($elem, true));
                }
            } elseif ($letter === 'T') {
                if (strlen($elem) == 1) {
                    $this->T = true;
                } elseif (preg_match('/^T(\d+[HMS])+$/', $elem)) {
                    preg_match_all('/(\d+)([HMS])/', $elem, $match);

                    if (count($match[1]) !== count(array_unique($match[2]))) {
                        throw new \Exception('Duplicate definitions in OCRA suite data input timestamp: ' . var_export($elem, true));
                    }

                    $length = 0;
                    for ($i = 0; $i < count($match[1]); $i++) {
                        $length += intval($match[1][$i]) * $this->TPeriods[$match[2][$i]];
                    }
                    if ($length <= 0) {
                        throw new \Exception('Invalid OCRA suite data input timestamp: ' . var_export($elem, true));
                    }

                    $this->T = true;
                    $this->TLength = $length;
                } else {
                    throw new \Exception('Invalid OCRA suite data input timestamp: ' . var_export($elem, true));
                }
            } else {
                throw new \Exception('Unsupported OCRA suite data input field: ' . var_export($elem, true));
            }
        }

        if (!$this->Q) {
            throw new \Exception('OCRA suite data input question not defined: ' . var_export($s[2], true));
        }
    }

    /**
     * Generate the challenge
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateChallenge()
    {
        $qLength = $this->QLength;
        $qType = $this->QType;

        $bytes = RandomGenerator::generateRandomBytes($qLength);

        switch ($qType) {
            case 'A':
                $challenge = base64_encode($bytes);
                $tr = implode("", unpack('H*', $bytes));
                $challenge = rtrim(strtr($challenge, '+/', $tr), '=');
                break;
            case 'H':
                $challenge = implode("", unpack('H*', $bytes));
                break;
            case 'N':
                $challenge = implode("", unpack('N*', $bytes));
                break;
            default:
                throw new \Exception('Unsupported OCRASuite challenge type: ' . var_export($qType, true));
                break;
        }

        $challenge = substr($challenge, 0, $qLength);

        return $challenge;
    }

    /**
     * Validate response using the challenge, the secret and the sessionKey
     *
     * @param string $response
     * @param string $challenge
     * @param string $secret
     * @param string $sessionKey
     *
     * @return boolean
     */
    public function validateResponse($response, $challenge, $secret, $sessionKey)
    {
        $ocra = new OATH_OCRA($this->getHash());
        $expected = $ocra->generateOCRA($this->OCRASuite, $secret, "", $challenge, "", $sessionKey, "");
        return $this->constEqual($expected, $response);
    }

    /**
     * Constant time string comparison, see http://codahale.com/a-lesson-in-timing-attacks/
     */
    private function constEqual($s1, $s2)
    {
        if (strlen($s1) != strlen($s2)) {
            return false;
        }

        $result = true;
        $length = strlen($s1);
        for ($i = 0; $i < $length; $i++) {
            $result &= ($s1[$i] == $s2[$i]);
        }

        return (boolean)$result;
    }
}

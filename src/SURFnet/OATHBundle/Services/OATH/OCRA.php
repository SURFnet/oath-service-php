<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\Services\OATH\RandomGenerator;
use SURFnet\OATHBundle\OATH\OCRA as OATH_OCRA;

class OCRA extends OATHService
{
    private $OCRASuite = NULL;

    private $OCRAVersion = NULL;

    private $CryptoFunctionType = NULL;
    private $CryptoFunctionHash = NULL;
    private $CryptoFunctionHashLength = NULL;
    private $CryptoFunctionTruncation = NULL;

    private $C = FALSE;
    private $Q = FALSE;
    private $QType = 'N';
    private $QLength = 8;

    private $P = FALSE;
    private $PType = 'SHA1';
    private $PLength = 20;

    private $S = FALSE;
    private $SLength = 64;

    private $T = FALSE;
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
     */
    private function parseOCRASuite($ocraSuite)
    {
        if (!is_string($ocraSuite)) {
            throw new \Exception('OCRASuite not in string format: ' . var_export($ocraSuite, TRUE));
        }

        $ocraSuite = strtoupper($ocraSuite);
        $this->OCRASuite = $ocraSuite;

        $s = explode(':', $ocraSuite);
        if (count($s) != 3) {
            throw new \Exception('Invalid OCRASuite format: ' . var_export($ocraSuite, TRUE));
        }

        $algo = explode('-', $s[0]);
        if (count($algo) != 2) {
            throw new \Exception('Invalid OCRA version: ' . var_export($s[0], TRUE));
        }

        if ($algo[0] !== 'OCRA') {
            throw new \Exception('Unsupported OCRA algorithm: ' . var_export($algo[0], TRUE));
        }

        if ($algo[1] !== '1') {
            throw new \Exception('Unsupported OCRA version: ' . var_export($algo[1], TRUE));
        }
        $this->OCRAVersion = $algo[1];

        $cf = explode('-', $s[1]);
        if (count($cf) != 3) {
            throw new \Exception('Invalid OCRA suite crypto function: ' . var_export($s[1], TRUE));
        }

        if ($cf[0] !== 'HOTP') {
            throw new \Exception('Unsupported OCRA suite crypto function: ' . var_export($cf[0], TRUE));
        }
        $this->CryptoFunctionType = $cf[0];

        if (!array_key_exists($cf[1], $this->supportedHashFunctions)) {
            throw new \Exception('Unsupported hash function in OCRA suite crypto function: ' . var_export($cf[1], TRUE));
        }
        $this->CryptoFunctionHash = $cf[1];
        $this->CryptoFunctionHashLength = $this->supportedHashFunctions[$cf[1]];

        if (!preg_match('/^\d+$/', $cf[2]) || (($cf[2] < 4 || $cf[2] > 10) && $cf[2] != 0)) {
            throw new \Exception('Invalid OCRA suite crypto function truncation length: ' . var_export($cf[2], TRUE));
        }
        $this->CryptoFunctionTruncation = intval($cf[2]);

        $di = explode('-', $s[2]);
        if (count($cf) == 0) {
            throw new \Exception('Invalid OCRA suite data input: ' . var_export($s[2], TRUE));
        }

        $data_input = array();
        foreach($di as $elem) {
            $letter = $elem[0];
            if (array_key_exists($letter, $data_input)) {
                throw new \Exception('Duplicate field in OCRA suite data input: ' . var_export($elem, TRUE));
            }
            $data_input[$letter] = 1;

            if ($letter === 'C' && strlen($elem) == 1) {
                $this->C = TRUE;
            } elseif ($letter === 'Q') {
                if (strlen($elem) == 1) {
                    $this->Q = TRUE;
                } elseif (preg_match('/^Q([AHN])(\d+)$/', $elem, $match)) {
                    $q_len = intval($match[2]);
                    if ($q_len < 4 || $q_len > 64) {
                        throw new \Exception('Invalid OCRA suite data input question length: ' . var_export($q_len, TRUE));
                    }
                    $this->Q = TRUE;
                    $this->QType = $match[1];
                    $this->QLength = $q_len;
                } else {
                    throw new \Exception('Invalid OCRA suite data input question: ' . var_export($elem, TRUE));
                }
            } elseif ($letter === 'P') {
                if (strlen($elem) == 1) {
                    $this->P = TRUE;
                } else {
                    $p_algo = substr($elem, 1);
                    if (!array_key_exists($p_algo, $this->supportedHashFunctions)) {
                        throw new \Exception('Unsupported OCRA suite PIN hash function: ' . var_export($elem, TRUE));
                    }
                    $this->P = TRUE;
                    $this->PType = $p_algo;
                    $this->PLength = $this->supportedHashFunctions[$p_algo];
                }
            } elseif ($letter === 'S') {
                if (strlen($elem) == 1) {
                    $this->S = TRUE;
                } elseif (preg_match('/^S(\d+)$/', $elem, $match)) {
                    $s_len = intval($match[1]);
                    if ($s_len <= 0 || $s_len > 512) {
                        throw new \Exception('Invalid OCRA suite data input session information length: ' . var_export($s_len, TRUE));
                    }

                    $this->S = TRUE;
                    $this->SLength = $s_len;
                } else {
                    throw new \Exception('Invalid OCRA suite data input session information length: ' . var_export($elem, TRUE));
                }
            } elseif ($letter === 'T') {
                if (strlen($elem) == 1) {
                    $this->T = TRUE;
                } elseif (preg_match('/^T(\d+[HMS])+$/', $elem)) {
                    preg_match_all('/(\d+)([HMS])/', $elem, $match);

                    if (count($match[1]) !== count(array_unique($match[2]))) {
                        throw new \Exception('Duplicate definitions in OCRA suite data input timestamp: ' . var_export($elem, TRUE));
                    }

                    $length = 0;
                    for ($i = 0; $i < count($match[1]); $i++) {
                        $length += intval($match[1][$i]) * $this->TPeriods[$match[2][$i]];
                    }
                    if ($length <= 0) {
                        throw new \Exception('Invalid OCRA suite data input timestamp: ' . var_export($elem, TRUE));
                    }

                    $this->T = TRUE;
                    $this->TLength = $length;
                } else {
                    throw new \Exception('Invalid OCRA suite data input timestamp: ' . var_export($elem, TRUE));
                }
            } else {
                throw new \Exception('Unsupported OCRA suite data input field: ' . var_export($elem, TRUE));
            }
        }

        if (!$this->Q) {
            throw new \Exception('OCRA suite data input question not defined: ' . var_export($s[2], TRUE));
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
        $q_length = $this->QLength;
        $q_type = $this->QType;

        $bytes = RandomGenerator::generateRandomBytes($q_length);

        switch($q_type) {
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
                throw new \Exception('Unsupported OCRASuite challenge type: ' . var_export($q_type, TRUE));
                break;
        }

        $challenge = substr($challenge, 0, $q_length);

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
	$ocra = new OATH_OCRA($this->getHash ()); 
        $expected = $ocra->generateOCRA($this->OCRASuite, $secret, "", $challenge, "", $sessionKey, "");
        return $this->constEqual($expected, $response);
    }

    /**
     * Constant time string comparison, see http://codahale.com/a-lesson-in-timing-attacks/
     */
    private function constEqual($s1, $s2)
    {
        if (strlen($s1) != strlen($s2)) {
            return FALSE;
        }

        $result = TRUE;
        $length = strlen($s1);
        for ($i = 0; $i < $length; $i++) {
            $result &= ($s1[$i] == $s2[$i]);
        }

        return (boolean)$result;
    }
}

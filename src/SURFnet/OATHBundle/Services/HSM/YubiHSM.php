<?php

namespace SURFnet\OATHBundle\Services\HSM;

use Symfony\Component\Process\ProcessBuilder;

class YubiHSM {

    private $device;
    private $keyHandle;

    public function __construct (array $options)
    {
        $this->device    = $options ['device'];
        $this->keyHandle = $options ['key_handle'];
        $this->commands  = $options ['commands'];
    }

    public function initOath ($secret)
    {
        $command = $this->commands['oath_init'];
        $args    = array(
            '--device',     $this->device,
            '--key-handle', $this->keyHandle,
            '--oath-k',     $secret,
        );

        $builder = new ProcessBuilder();
        $process = $builder->setPrefix($command)
            ->setArguments($args)
            ->getProcess();

        $process->mustRun();
        return $process->getOutput();
    }

    public function sha1Hmac ($aead, $nonce, $data)
    {
        $command = $this->commands['hash_aead'];
        $args    = array(
            '--device',     $this->device,
            '--key-handle', $this->keyHandle,
            '--aead',       $aead,
            '--nonce',      $nonce,
            '--data',       bin2hex($data),
        );

        $builder = new ProcessBuilder();
        $process = $builder->setPrefix($command)
            ->setArguments($args)
            ->getProcess();

        $process->mustRun();
        return $process->getOutput();
    }

    public function validateHOTP ($aead, $nonce, $counter, $token)
    {
        $command = $this->commands['oath_hotp_validate'];
        $args    = array(
            '--device',     $this->device,
            '--key-handle', $this->keyHandle,
            '--aead',       $aead,
            '--nonce',      $nonce,
            '--counter',    $counter,
            '--token',      $token,
        );

        $builder = new ProcessBuilder();
        $process = $builder->setPrefix($command)
            ->setArguments($args)
            ->getProcess();

        $process->mustRun();
    }
} 
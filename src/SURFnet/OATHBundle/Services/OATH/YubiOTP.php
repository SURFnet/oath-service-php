<?php

namespace SURFnet\OATHBundle\Services\OATH;

use SURFnet\OATHBundle\Services\UserStorage\UserStorageAbstract;
use Symfony\Component\Process\ProcessBuilder;

class YubiOTP extends OATHService
{
    /**
     * Validate response using the
     *
     * @param string                $otp
     * @param string                $userId
     * @param UserStorageAbstract   $userStorage
     *
     * @return boolean
     */
    public function validateResponse($otp, $userId, UserStorageAbstract $userStorage)
    {
        if (!isset($this->options['yhsm_validate_otp'])) {
            throw new \RuntimeException('Missing path to yhsm-validate-otp script');
        }

        $command = $this->options['yhsm_validate_otp'];

	    if (!file_exists($command) || !is_executable($command)) {
		    throw new \RuntimeException("The command($command) does not exist or is not executable");
	    }

	    // Pass in the OTP key
        $args = array('--otp', $otp);

	    // Pass in the device address if set
	    if (isset($this->options['device'])) {
		    $args[] = '--device';
		    $args[] = $this->options['device'];
	    }

	    // Build the process
        $builder = new ProcessBuilder();
        $process = $builder->setPrefix($command)
                           ->setArguments($args)
                           ->getProcess();

	    return ($process->run() === 0);
    }
}
<?php

namespace SURFnet\OATHBundle\Controller;

use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use SURFnet\OATHBundle\Services\OATH\HOTP;
use SURFnet\OATHBundle\Services\OATH\OCRA;
use SURFnet\OATHBundle\Services\OATH\TOTP;
use SURFnet\OATHBundle\Services\UserStorage\PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BaseController extends AbstractFOSRestController
{
    protected $parameters;

    protected $ocra;

    protected $hotp;

    protected $totp;

    protected $userStorage;

    public function __construct(
        ParameterBagInterface $parameterBag,
        OCRA $ocra,
        HOTP $hotp,
        TOTP $totp,
        PDO $userStorage
    ) {
        $this->parameters = $parameterBag;
        $this->userStorage = $userStorage;
        $this->ocra = $ocra;
        $this->hotp = $hotp;
        $this->totp = $totp;
    }
    /**
     * Verify the consumer key that should be present in the headers
     *
     * @throws Exception
     */
    protected function verifyConsumerKey()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $consumerKey = $request->headers->get('x-oathservice-consumerkey');
        $config = $this->parameters->get('surfnet_oath.consumerKey');
        if ($consumerKey != $config) {
            throw new Exception("invalid_consumerkey", 401);
        }
    }
}

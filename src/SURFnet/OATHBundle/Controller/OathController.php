<?php

namespace SURFnet\OATHBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

class OathController extends FOSRestController
{
    /**
     * @Get("/oath/challenge/ocra")
     * @ApiDoc(
     *  section="OATH",
     *  description="Get an OCRA challenge",
     *  statusCodes={
     *      200="Success, challenge is in the body",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function getOcraChallengeAction()
    {
        return $this->getChallenge('ocra');
    }

    /**
     * @Get("/oath/challenge/hotp")
     * @ApiDoc(
     *  section="OATH",
     *  description="Get an HOTP challenge",
     *  statusCodes={
     *      200="Success, challenge is in the body",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function getHotpChallengeAction()
    {
        return $this->getChallenge('hotp');
    }

    /**
     * @Get("/oath/challenge/totp")
     * @ApiDoc(
     *  section="OATH",
     *  description="Get an TOTP challenge",
     *  statusCodes={
     *      200="Success, challenge is in the body",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function getTotpChallengeAction()
    {
        return $this->getChallenge('totp');
    }

    /**
     * @Get("/oath/validate/ocra")
     * @ApiDoc(
     *  section="OATH",
     *  description="Validate a OCRA challenge against a response",
     *  parameters={
     *    {"name"="challenge", "dataType"="string", "required"=true, "description"="The original challenge generated by GET /oath/challenge/ocra"},
     *    {"name"="response", "dataType"="string", "required"=true, "description"="The response to validate the challenge against"},
     *    {"name"="userId", "dataType"="string", "required"=true, "description"="The user id"},
     *    {"name"="sessionKey", "dataType"="string", "required"=true, "description"="The session key"}
     *  },
     *  statusCodes={
     *      200="Success",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function validateOcraChallengeAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        return $this->validateChallenge('ocra', $request->get('response'), $request->get('challenge'), $request->get('userId'), $request->get('sessionKey'));
    }

    /**
     * @Get("/oath/validate/hotp")
     * @ApiDoc(
     *  section="OATH",
     *  description="Validate a HOTP challenge against a response",
     *  parameters={
     *    {"name"="challenge", "dataType"="string", "required"=true, "description"="The original challenge generated by GET /oath/challenge/hotp"},
     *    {"name"="response", "dataType"="string", "required"=true, "description"="The response to validate the challenge against"},
     *  },
     *  statusCodes={
     *      200="Success",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function validateHotpChallengeAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        return $this->validateChallenge('hotp', $request->get('response'), $request->get('challenge'));
    }

    /**
     * @Get("/oath/validate/totp")
     * @ApiDoc(
     *  section="OATH",
     *  description="Validate a TOTP challenge against a response",
     *  parameters={
     *    {"name"="challenge", "dataType"="string", "required"=true, "description"="The original challenge generated by GET /oath/challenge/totp"},
     *    {"name"="response", "dataType"="string", "required"=true, "description"="The response to validate the challenge against"},
     *  },
     *  statusCodes={
     *      200="Success",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function validateTotpChallengeAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        return $this->validateChallenge('ocra', $request->get('response'), $request->get('challenge'));
    }

    /**
     * Get the challenge for the correct type
     *
     * @param string $type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getChallenge($type)
    {
        $oathservice = $this->getOATHService($type);
        $responseCode = 200;
        try {
            $data = $oathservice->generateChallenge();
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }

    /**
     * Validate the challenge against the given response
     *
     * @param string $type
     * @param string $response
     * @param string $challenge
     * @param string $userId
     * @param string $sessionKey
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function validateChallenge($type, $response, $challenge, $userId = null, $sessionKey = null)
    {
        $oathservice = $this->getOATHService($type);
        $responseCode = 200;
        try {
            $data = $oathservice->validateResponse($response, $challenge, $userId, $sessionKey);
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }

    /**
     * Create the storage class using the storage factory and return the class
     *
     * @param string $type
     *
     * @return mixed
     */
    protected function getOATHService($type)
    {
        $oathFactory = $this->get('surfnet_oath.oath.factory');
        //$config = $this->container->getParameter('surfnet_oath');
        return $oathFactory->createOATHService($type, array());
    }
}
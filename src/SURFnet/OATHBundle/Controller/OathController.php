<?php

namespace SURFnet\OATHBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

class OathController extends BaseController
{
    /**
     * @Get("/oath/challenge/ocra")
     * @ApiDoc(
     *  section="OATH",
     *  description="Get an OCRA challenge",
     *  statusCodes={
     *      200="Success, challenge is in the body",
     *      401="Invalid consumer key",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function getOcraChallengeAction()
    {
        $responseCode = 200;
        try {
            $this->verifyConsumerKey();
            $oathservice = $this->getOATHService('ocra');
            $data = $oathservice->generateChallenge();
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
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
     *      204="Success, challenge is valid",
     *      400="Challenge is not valid",
     *      401="Invalid consumer key",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function validateOcraChallengeAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $responseCode = 204;
        $data = null;
        try {
            $this->verifyConsumerKey();
            $oathservice = $this->getOATHService('ocra');
            $secret = null;
            $userId = $request->get('userId');
            if ($userId !== null) {
                $userStorage = $this->getUserStorage();
                $secret = $userStorage->getSecret($userId);
            }
            $result = $oathservice->validateResponse($request->get('response'), $request->get('challenge'), $secret, $request->get('sessionKey'));
            if (!$result) {
                $responseCode = 400;
            }
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString(),);
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }

    /**
     * @Get("/oath/validate/hotp")
     * @ApiDoc(
     *  section="OATH",
     *  description="Validate a HOTP challenge against a response",
     *  parameters={
     *    {"name"="response", "dataType"="string", "required"=true, "description"="The response to validate"},
     *    {"name"="userId", "dataType"="string", "required"=true, "description"="The user id"},
     *  },
     *  statusCodes={
     *      204="Success, challenge is valid",
     *      400="Challenge is not valid",
     *      401="Invalid consumer key",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function validateHotpChallengeAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $responseCode = 204;
        $data = null;
        try {
            $this->verifyConsumerKey();
            $oathservice = $this->getOATHService('hotp');
            $result = $oathservice->validateResponse($request->get('response'), $request->get('userId'), $this->getUserStorage());
            if (!$result) {
                $responseCode = 400;
            }
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString(),);
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }

    /**
     * @Get("/oath/validate/totp")
     * @ApiDoc(
     *  section="OATH",
     *  description="Validate a TOTP challenge against a response",
     *  parameters={
     *    {"name"="response", "dataType"="string", "required"=true, "description"="The response to validate the challenge against"},
     *    {"name"="userId", "dataType"="string", "required"=true, "description"="The user id"},
     *  },
     *  statusCodes={
     *      204="Success, challenge is valid",
     *      400="Challenge is not valid",
     *      401="Invalid consumer key",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function validateTotpChallengeAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $responseCode = 204;
        $data = null;
        try {
            $this->verifyConsumerKey();
            $oathservice = $this->getOATHService('totp');
            $result = $oathservice->validateResponse($request->get('response'), $request->get('userId'), $this->getUserStorage());
            if (!$result) {
                $responseCode = 400;
            }
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString(),);
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
        return $this->get("surfnet_oath.oath.service.{$type}");
    }
}

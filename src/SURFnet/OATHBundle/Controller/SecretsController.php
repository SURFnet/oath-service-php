<?php

namespace SURFnet\OATHBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SecretsController extends BaseController
{
    /**
     * @ApiDoc(
     *  section="Secrets",
     *  description="Set or update user's secret",
     *  requirements={
     *    {"name"="identifier", "dataType"="string", "description"="The identifier for the secret"}
     *  },
     *  parameters={
     *    {"name"="secret", "dataType"="string", "required"=true, "description"="The user's secret"},
     *  },
     *  statusCodes={
     *      204="Success, empty body",
     *      401="Invalid consumer key",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function postSecretsAction($identifier)
    {
        $responseCode = 200;
        try {
            $this->verifyConsumerKey();
            $request = $this->get('request_stack')->getCurrentRequest();
            $userStorage = $this->getUserStorage();
            $data = $userStorage->saveSecret($identifier, $request->get('secret'));
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  section="Secrets",
     *  description="Delete a specific secret",
     *  requirements={
     *    {"name"="identifier", "dataType"="string", "description"="The identifier for the secret"}
     *  },
     *  statusCodes={
     *      204="Secret is deleted",
     *      401="Invalid consumer key",
     *      404="Identifier not found",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function deleteSecretsAction($identifier)
    {
        $responseCode = 200;
        try {
            $this->verifyConsumerKey();
            $userStorage = $this->getUserStorage();
            $data = $userStorage->deleteSecret($identifier);
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }
}

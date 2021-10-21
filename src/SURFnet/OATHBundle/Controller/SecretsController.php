<?php

namespace SURFnet\OATHBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SecretsController extends BaseController
{
    /**
     * @View()
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
        $responseCode = 204;
        $data = null;
        try {
            $this->verifyConsumerKey();
            $request = $this->get('request_stack')->getCurrentRequest();
            $this->userStorage->saveSecret($identifier, $request->get('secret'));
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        return $this->view($data, $responseCode);
    }

    /**
     * @View()
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
        $responseCode = 204;
        try {
            $this->verifyConsumerKey();
            $data = $this->userStorage->deleteSecret($identifier);
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        return $this->view($data, $responseCode);
    }
}

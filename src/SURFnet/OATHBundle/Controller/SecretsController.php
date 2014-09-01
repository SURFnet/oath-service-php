<?php

namespace SURFnet\OATHBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SecretsController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  section="Secrets",
     *  description="Get the user secret, returns the secret",
     *  requirements={
     *    {"name"="identifier", "dataType"="string", "description"="The identifier for the secret"}
     *  },
     *  statusCodes={
     *      200="Success, secret is in the body",
     *      404="Identifier not found",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function getSecretsAction($identifier)
    {
        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

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
     *      200="Success",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function postSecretsAction($identifier)
    {
        $view = $this->view(array(), 200);
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
     *      404="Identifier not found",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function deleteSecretsAction($identifier)
    {
        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }
}

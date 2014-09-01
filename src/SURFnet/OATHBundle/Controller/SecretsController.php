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
        $userStorage = $this->getUserStorage();
        $responseCode = 200;
        try {
            $data = $userStorage->getSecret($identifier);
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
        $request = $this->get('request_stack')->getCurrentRequest();
        $userStorage = $this->getUserStorage();
        $responseCode = 200;
        try {
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
     *      404="Identifier not found",
     *      500="General error, something went wrong",
     *  },
     * )
     */
    public function deleteSecretsAction($identifier)
    {
        $userStorage = $this->getUserStorage();
        $responseCode = 200;
        try {
            $data = $userStorage->deleteSecret($identifier);
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
    protected function getUserStorage()
    {
        $userStorageFactory = $this->get('surfnet_oath.userstorage.factory');
        $config = $this->container->getParameter('surfnet_oath');
        return $userStorageFactory->createUserStorage($config['userstorage']['type'], $config['userstorage']['options']);
    }
}

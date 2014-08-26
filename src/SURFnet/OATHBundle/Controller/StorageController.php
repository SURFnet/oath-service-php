<?php

namespace SURFnet\OATHBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class StorageController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  section="Storage",
     *  description="Retrieve a value from the storage",
     *  requirements={
     *    {"name"="key", "dataType"="string", "description"="The key that is used to identify the value"}
     *  },
     *  statusCodes={
     *      200="Success, value is in the body",
     *      400="Value is expired",
     *      404="Key not found",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\Storage"
     * )
     */
    public function getStorageAction($key)
    {
        $storage = $this->getStorage();
        $responseCode = 200;
        try {
            $data = $storage->getValue($key);
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  section="Storage",
     *  description="Store a value in the storage",
     *  requirements={
     *    {"name"="key", "dataType"="string", "description"="The key that is used to identify the value"}
     *  },
     *  parameters={
     *    {"name"="value", "dataType"="string", "required"=true, "description"="The value to store"},
     *    {"name"="expire", "dataType"="integer", "required"=false, "description"="Number of seconds to expire the value, default 0 (unlimited)"}
     *  },
     *  statusCodes={
     *      200="Success",
     *      404="Key not found",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\Storage"
     * )
     */
    public function putStorageAction($key)
    {
        $storage = $this->getStorage();
        $request = $this->get('request_stack')->getCurrentRequest();

        $responseCode = 200;
        try {
            $data = $storage->storeValue($key, $request->get('value'), (int)$request->get('expire', 0));
        } catch (\Exception $e) {
            $data = array('error' => $e->getMessage());
            $responseCode = $e->getCode() ?: 500;
        }
        $view = $this->view($data, $responseCode);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  section="Storage",
     *  description="Delete a specific value from the storage",
     *  requirements={
     *    {"name"="key", "dataType"="string", "description"="The key to identify the value to be deleted"}
     *  },
     *  statusCodes={
     *      204="Value is deleted",
     *      404="Key not found",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\Storage"
     * )
     */
    public function deleteStorageAction($key)
    {
        $storage = $this->getStorage();
        $responseCode = 200;
        try {
            $data = $storage->deleteValue($key);
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
     * @return mixed
     */
    protected function getStorage()
    {
        $storageFactory = $this->get('surfnet_oath.storage.factory');
        $config = $this->container->getParameter('surfnet_oath');
        return $storageFactory->createStorage($config['storage']['type'], $config['storage']['options']);
    }
}
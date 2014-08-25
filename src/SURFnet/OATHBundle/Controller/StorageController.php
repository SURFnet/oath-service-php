<?php

namespace SURFnet\OATHBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
     *      404="Key not found",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\Storage"
     * )
     */
    public function getStorageAction($key)
    {
        $view = $this->view(array(), 200);
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
     *    {"name"="value", "dataType"="string", "required"=true, "description"="The value to store"}
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
        $view = $this->view(array(), 200);
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
        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }
}
<?php

namespace SURFnet\OATHBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

class BaseController extends FOSRestController
{
    /**
     * Verify the consumer key that should be present in the headers
     *
     * @throws \Exception
     */
    protected function verifyConsumerKey()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $consumerKey = $request->headers->get('x-oathservice-consumerkey');
        $config = $this->container->getParameter('surfnet_oath');
        if ($consumerKey != $config['consumerKey']) {
            throw new \Exception("invalid_consumerkey", 401);
        }
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
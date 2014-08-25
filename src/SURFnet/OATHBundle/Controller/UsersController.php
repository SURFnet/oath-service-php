<?php

namespace SURFnet\OATHBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UsersController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  section="Users",
     *  description="Get a specific user, returns a json representation of a user object",
     *  requirements={
     *    {"name"="id", "dataType"="string", "description"="The id of the user"}
     *  },
     *  statusCodes={
     *      200="Success, user details are in the body",
     *      404="User not found",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\User"
     * )
     */
    public function getUsersAction($id)
    {
        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  section="Users",
     *  description="Create a new user, returns a json representation of a user object",
     *  parameters={
     *    {"name"="id", "dataType"="string", "required"=true, "description"="The user's id"},
     *    {"name"="displayName", "dataType"="string", "required"=true, "description"="The user's name"},
     *  },
     *  statusCodes={
     *      201="Created, user details are in the body",
     *      400="User with this id already exists",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\User"
     * )
     */
    public function postUsersAction()
    {
        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  section="Users",
     *  description="Update information about a user, returns a json representation of a user object",
     *  requirements={
     *    {"name"="id", "dataType"="string", "description"="The id of the user"}
     *  },
     *  parameters={
     *    {"name"="displayName", "dataType"="string", "required"=false, "description"="The user's name"},
     *    {"name"="loginAttempts", "dataType"="integer", "required"=false, "description"="Number of times the user tried to login, default 0"},
     *    {"name"="blocked", "dataType"="boolean", "required"=false, "description"="Is the user blocked, default to false"},
     *    {"name"="secret", "dataType"="string", "required"=false, "description"="The user's secret"},
     *    {"name"="notificationType", "dataType"="string", "required"=false, "description"="The user's notification type"},
     *    {"name"="notificationAddress", "dataType"="string", "required"=false, "description"="The user's notification address"},
     *    {"name"="temporaryBlockAttempts", "dataType"="integer", "required"=false, "description"="Number of login attempts that will cause a temporary block eventually, default 0"},
     *    {"name"="temporaryBlockTimestamp", "dataType"="string", "required"=false, "format"="Timestamp format should be something like 2014-08-25T12:00:00Z", "description"="Timestamp when the temporary block started"},
     *  },
     *  statusCodes={
     *      200="Success, user details are in the body",
     *      404="User not found",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\User"
     * )
     */
    public function putUsersAction($id)
    {
        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  section="Users",
     *  description="Delete a specific user",
     *  requirements={
     *    {"name"="id", "dataType"="string", "description"="The id of the user to delete"}
     *  },
     *  statusCodes={
     *      204="User is deleted",
     *      404="User not found",
     *      500="General error, something went wrong",
     *  },
     *  return="SURFnet\OATHBundle\Entity\User"
     * )
     */
    public function deleteUsersAction($id)
    {
        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }
}

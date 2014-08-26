<?php

namespace SURFnet\OATHBundle\Services\User;

interface UserInterface
{
    /**
     * Store a new user with id and a displayName.
     *
     * @param string $id
     * @param string $displayName
     *
     * @return \SURFnet\OATHBundle\Entity\User
     */
    public function createUser($id, $displayName);

    /**
     * Get the user details.
     *
     * @param string $id
     *
     * @return \SURFnet\OATHBundle\Entity\User
     */
    public function getUser($id);

    /**
     * Update the user details.
     *
     * @param string $id
     *
     * @return \SURFnet\OATHBundle\Entity\User
     */
    public function updateUser($id, $data);

    /**
     * Delete the user.
     *
     * @param string $id
     */
    public function deleteUser($id);
}
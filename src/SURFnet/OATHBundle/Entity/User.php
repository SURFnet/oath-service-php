<?php

namespace SURFnet\OATHBundle\Entity;

class User
{
    /**
     * Id of the user
     * @var string
     */
    public $id;

    /**
     * Display name of the user
     * @var string
     */
    public $displayName;

    /**
     * Number of attempts to login
     * @var integer
     */
    public $loginAttempts;

    /**
     * Is the user blocked?
     * @var boolean
     */
    public $blocked;

    /**
     * Secret of the user
     * @var string
     */
    public $secret;

    /**
     * The notification type
     * @var string
     */
    public $notificationType;

    /**
     * The notification address
     * @var string
     */
    public $notificationAddress;

    /**
     * Number of login attempts that will cause a temporary block eventually
     * @var integer
     */
    public $temporaryBlockedAttempts;

    /**
     * Timstamp when the temporary block was started
     * @var string
     */
    public $temporaryBlockTimestamp;
}
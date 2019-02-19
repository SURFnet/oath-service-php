<?php

namespace SURFnet\OATHBundle\Services\UserStorage;

use SURFnet\OATHBundle\Services\UserStorage\Encryption\Dummy as Dummy;
use SURFnet\OATHBundle\Services\UserStorage\Encryption\Openssl as Mcrypt;

/**
 * Class PDO storage
 * Table structure (for mysql):
 * CREATE TABLE `storage` (
 *   `identifier` varchar(100) NOT NULL,
 *   `secret` varchar(255) NOT NULL,
 *   `counter` int(10) DEFAULT '0',
 *   PRIMARY KEY (`identifier`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 */
class PDO extends UserStorageAbstract
{
    /**
     * The PDO handle for database queries
     * @var null|\PDO
     */
    protected $handle = null;

    /**
     * The table name used to store the identifier/secret pairs
     * @var string
     */
    protected $tablename;

    /**
     * The encryption instance
     *
     * @var UserEncryptionInterface
     */
    protected $encryption;

    /**
     * Initialize pdo handle
     */
    public function init()
    {
        $this->handle = new \PDO($this->options['dsn'], $this->options['username'], $this->options['password']);
        $this->tablename = $this->options["table"];

        if (isset($this->options['encryption']) && isset($this->options['encryption']['type'])) {
            $class = '\SURFnet\OATHBundle\Services\UserStorage\Encryption\\'.ucfirst($this->options['encryption']['type']);
            $options = (isset($this->options['encryption']['options']) ? $this->options['encryption']['options'] : null);
            $this->encryption = new $class($options);
        } else {
            $this->encryption = new Dummy(null);
        }
    }

    /**
     * Get the users secret
     *
     * @param string $identifier
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getSecret($identifier)
    {
        if ($this->identifierExists($identifier)) {
            $sth = $this->handle->prepare("SELECT `secret` FROM ".$this->tablename." WHERE `identifier` = ?");
            $sth->execute(array($identifier));
            $result = $this->encryption->decrypt($sth->fetchColumn());
            if ($result) {
                return $result;
            }
        } else {
            throw new \Exception('identifier_not_found', 404);
        }
        return null;
    }

    /**
     * Get the users secret and counter
     *
     * @param string $identifier
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getSecretInfo($identifier)
    {
        if ($this->identifierExists($identifier)) {
            $sth = $this->handle->prepare("SELECT `secret`, `counter` FROM ".$this->tablename." WHERE `identifier` = ?");
            $sth->execute(array($identifier));
            $result = $sth->fetch();
            if ($result) {
                return $result;
            }
        } else {
            throw new \Exception('identifier_not_found', 404);
        }
        return null;
    }

    /**
     * Save the secret
     *
     * @param string $identifier
     * @param string $secret
     */
    public function saveSecret($identifier, $secret)
    {
        if ($this->identifierExists($identifier)) {
            $sth = $this->handle->prepare("UPDATE ".$this->tablename." SET `secret` = ? WHERE `identifier` = ?");
        } else {
            $sth = $this->handle->prepare("INSERT INTO ".$this->tablename." (`secret`,`identifier`) VALUES (?,?)");
        }
        $sth->execute(array($this->encryption->encrypt($secret), $identifier));
    }

    /**
     * Update the user's counter (if possible, used for HOTP validation)
     *
     * @param string $identifier
     *
     * @throws \Exception
     */
    public function updateCounter($identifier)
    {
        if ($this->identifierExists($identifier)) {
            $sth = $this->handle->prepare("UPDATE ".$this->tablename." SET `counter` = counter + 1 WHERE `identifier` = ?");
            $sth->execute(array($identifier));
        } else {
            throw new \Exception('identifier_not_found', 404);
        }
    }

    /**
     * Delete the secret
     *
     * @param string $identifier
     *
     * @throws \Exception
     */
    public function deleteSecret($identifier)
    {
        if ($this->identifierExists($identifier)) {
            $sth = $this->handle->prepare("DELETE FROM ".$this->tablename." WHERE `identifier` = ?");
            $sth->execute(array($identifier));
        } else {
            throw new \Exception('identifier_not_found', 404);
        }
    }

    /**
     * Does the identifier exists in the database
     *
     * @param string $identifier
     *
     * @return mixed
     */
    private function identifierExists($identifier)
    {
        $sth = $this->handle->prepare("SELECT `identifier` FROM ".$this->tablename." WHERE `identifier` = ?");
        $sth->execute(array($identifier));
        return $sth->fetchColumn();
    }
}
<?php

namespace SURFnet\OATHBundle\Services\UserStorage;

class PDO extends UserStorageAbstract
{
    /**
     * The PDO handle for database queries
     * @var null|Pdo
     */
    protected $handle = null;

    /**
     * The table name used to store the identifier/secret pairs
     * @var string
     */
    protected $tablename;

    /**
     * Initialize pdo handle
     */
    public function init()
    {
        $this->handle = new \PDO($this->options['dsn'], $this->options['username'], $this->options['password']);
        $this->tablename = $this->options["table"];
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
            $result = unserialize($sth->fetchColumn());
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
        $sth->execute(array(serialize($secret), $identifier));
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
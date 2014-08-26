<?php

namespace SURFnet\OATHBundle\Services\Storage;

class Pdo extends StorageAbstract
{
    /**
     * The PDO handle for database queries
     * @var null|Pdo
     */
    protected $handle = null;

    /**
     * The table name used to store the key/value pairs
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
     * Does the key exists in the database
     *
     * @param string $key
     *
     * @return mixed
     */
    private function keyExists($key)
    {
        $sth = $this->handle->prepare("SELECT `key` FROM ".$this->tablename." WHERE `key` = ?");
        $sth->execute(array($key));
        return $sth->fetchColumn();
    }

    /**
     * Cleanup the expired records in the database
     */
    private function cleanExpired() {
        $sth = $this->handle->prepare("DELETE FROM ".$this->tablename." WHERE `expire` < ?");
        $sth->execute(array(time()));
    }

    /**
     * Store a value using key as identifier (overwrites value if key already exists).
     *
     * @param string  $key
     * @param string  $value
     * @param integer $expire
     *
     * @return \SURFnet\OATHBundle\Entity\Storage
     */
    public function storeValue($key, $value, $expire = 0)
    {
        if ($this->keyExists($key)) {
            $sth = $this->handle->prepare("UPDATE ".$this->tablename." SET `value` = ?, `expire` = ? WHERE `key` = ?");
        } else {
            $sth = $this->handle->prepare("INSERT INTO ".$this->tablename." (`value`,`expire`,`key`) VALUES (?,?,?)");
        }
        $sth->execute(array(serialize($value),time()+$expire,$key));
        return $this->returnStorage($key, $value);
    }

    /**
     * Get the value from the storage identified by the key.
     *
     * @param string $key
     *
     * @return \SURFnet\OATHBundle\Entity\Storage
     *
     * @throws \Exception
     */
    public function getValue($key)
    {
        if (rand(0, 1000) < 10) {
            $this->cleanExpired();
        }
        if ($this->keyExists($key)) {
            $sth = $this->handle->prepare("SELECT `value` FROM ".$this->tablename." WHERE `key` = ?");
            $sth->execute(array($key));
            $result = unserialize($sth->fetchColumn());
            return $this->returnStorage($key, $result);
        } else {
            throw new \Exception('key_not_found', 404);
        }
        return NULL;
    }

    /**
     * Delete the value from the storage identified by the key.
     *
     * @param string $key
     *
     * @throws \Exception
     */
    public function deleteValue($key)
    {
        if ($this->keyExists($key)) {
            $sth = $this->handle->prepare("DELETE FROM ".$this->tablename." WHERE `key` = ?");
            $sth->execute(array($key));
        } else {
            throw new \Exception('key_not_found', 404);
        }
    }
}
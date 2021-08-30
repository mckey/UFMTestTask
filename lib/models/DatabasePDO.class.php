<?php

class DatabasePDO extends Database
{
    public function connect()
    {
        try {
            $this->connection = new \PDO(
                'mysql:host=' . $this->parameters['host'] . ';dbname=' . $this->parameters['name'],
                $this->parameters['username'],
                $this->parameters['password'],
                [
                    \PDO::ATTR_PERSISTENT => true
                ]
            );

            $this->is_connected = true;
        } catch (PDOException $e) {
            $this->is_connected = false;
        }
    }

    public function isDatabaseExists(string $database_name): bool
    {
        try {
            $result = $this
                ->getConnection()
                ->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database_name'");
        } catch (PDOException $e) {
            return false;
        }

        return $result !== false;
    }

    public function execute(string $sql): PDOStatement
    {
       return $this->getConnection()->query($sql);
    }

    public function disconnect()
    {

    }
}

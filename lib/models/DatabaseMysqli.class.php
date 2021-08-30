<?php

class DatabaseMysqli extends Database
{
    public function connect()
    {
        if ($this->is_connected) {
            return;
        }

        $mysql_connection = mysqli_connect(
            $this->parameters['host'],
            $this->parameters['username'],
            $this->parameters['password'],
        );

        if ($mysql_connection !== false) {
            $this->is_connected = true;
            $this->connection = $mysql_connection;
        } else {
            $this->is_connected = false;

            printf("Не удалось подключиться: %s\n", mysqli_connect_error());

            exit();
        }
    }

    public function isDatabaseExists(string $database_name): bool
    {
        return $this->getConnection()->select_db($database_name);
    }

    public function execute(string $sql): mysqli_result|bool
    {
        return $this->getConnection()->query($sql);
    }

    public function disconnect()
    {
        if ($this->is_connected) {
            mysqli_close($this->connection);
        }
    }
}

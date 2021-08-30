<?php

abstract class Database implements DatabaseInterface
{
    protected array $parameters;

    private static array $supported_drivers = ['mysqli', 'pdo'];

    /* @var mysqli|PDO */
    protected mysqli|PDO $connection;
    protected bool $is_connected = false;

    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
    }

    abstract function connect();

    public function getConnection(): PDO|mysqli
    {
        if (!isset($this->connection)) {
            $this->connect();
        }

        return $this->connection;
    }

    public function getParam(string $param_name) : ?string
    {
        return $this->parameters[$param_name] ?? null;
    }

    abstract function execute(string $sql);

    abstract function disconnect();
}

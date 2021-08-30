<?php

use JetBrains\PhpStorm\Pure;

class Config
{
    protected string $config_file = 'config.json';
    protected string $database_schema = 'schema.sql';
    protected array $database_params;
    protected DatabasePDO|DatabaseMysqli $database;

    public function __construct()
    {
        $this->database_params = json_decode(file_get_contents($this->getConfigFilePath()), true);
    }

    public function getDatabase(): DatabasePDO|DatabaseMysqli
    {
        if (!isset($this->database)) {
            $this->database = match ($this->database_params['driver']) {
                'mysqli' => new DatabaseMysqli($this->database_params),
                'pdo' => new DatabasePDO($this->database_params),
            };
        }

        return $this->database;
    }

    public function getConfigFilePath(): string
    {
        return __DIR__ . '/../../config/' . $this->config_file;
    }

    public function getDatabaseSchemaPath(): string
    {
        return __DIR__ . '/../../config/' . $this->database_schema;
    }
}

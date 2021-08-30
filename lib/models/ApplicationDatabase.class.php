<?php

class ApplicationDatabase
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->checkDatabaseAndTable();
    }

    private function checkDatabaseAndTable()
    {
        $is_database_exist = $this->getDatabase()->isDatabaseExists($this->getDatabase()->getParam('name'));

        if ($is_database_exist) {
            $result = $this->getDatabase()->execute('SHOW TABLES LIKE banner_view_stat');

            if ($result !== false && $result->num_rows !== 0) {
                return;
            }
        }

        $this->createDatabaseAndTable();
    }

    private function createDatabaseAndTable()
    {
        $this->getDatabase()->execute($this->getConfig()->getDatabaseSchemaPath());
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getDatabase(): DatabasePDO|DatabaseMysqli
    {
        return $this->config->getDatabase();
    }

    public function addBannerViewStat(array $params)
    {
        $connection = $this->getDatabase()->getConnection();

        array_map(function ($param) use ($connection) {
            return mysqli_real_escape_string($connection, $param);
        }, $params);

        $this->getDatabase()->getConnection()->query(
            "INSERT INTO banner_view_stat (ip_address, user_agent, view_date, page_url) 
             VALUES ('{$params['ip_address']}', '{$params['user_agent']}', NOW(), '{$params['page_url']}')
             ON DUPLICATE KEY UPDATE 
                view_date = NOW(),
                views_count = views_count + 1"
        );
    }
}

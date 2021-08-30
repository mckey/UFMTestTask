<?php

class Application
{
    protected Config $config;
    protected ApplicationDatabase $database;
    protected WebRequest $request;

    public function __construct()
    {
        $this->config = new Config();

        $this->database = new ApplicationDatabase($this->getConfig());

        $this->request = new WebRequest([
            'path_info_array' => 'ENV',
            'http_port' => 50080,
        ]);
    }

    public function execute(string $action)
    {
        echo $this->{'execute' . ucfirst($action)}();
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getDatabase(): ApplicationDatabase
    {
        return $this->database;
    }

    public function getRequest(): WebRequest
    {
        return $this->request;
    }
}

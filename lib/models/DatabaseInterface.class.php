<?php

interface DatabaseInterface
{
    public function connect();

    public function getConnection();

    public function execute(string $sql);

    public function disconnect();
}

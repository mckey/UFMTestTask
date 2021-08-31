<?php

spl_autoload_register(
    function ($class_name) {
        $path = __DIR__ . "/models/$class_name.class.php";

        if (file_exists($path)) {
            require_once $path;
        }
    }
);

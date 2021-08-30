<?php

class ApplicationRouter
{
    public static function forwardTo(string $application, string $action)
    {
        $application_class = ucfirst($application) . 'Application';
        $application_class_file =  __DIR__ . '/../../app/' . $application_class . '.class.php';
        
        if (!file_exists($application_class_file)) {
            throw new \InvalidArgumentException(sprintf('The application "%s" does not exist.', $application_class_file));
        }

        require_once $application_class_file;

        $application = new $application_class();

        echo $application->{'execute' . ucfirst($action)}();
    }
}

<?php

$defaults = [
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'proyecto de callamullo',
    'db_user' => 'root',
    'db_pass' => '',
];

$localFile = __DIR__ . '/config.local.php';
if (file_exists($localFile)) {
    $local = require $localFile;
    if (is_array($local)) {
        return array_merge($defaults, $local);
    }
}

return $defaults;

<?php
return [
    'app' => [
        'name'     => 'BiblioPoás',
        'env'      => 'local',
        'timezone' => 'America/Costa_Rica',
        'base_url' => '/',
    ],
    'db' => [
        'host'     => '127.0.0.1',
        'database' => 'biblio_poas',
        'user'     => 'root',
        'password' => 'root',
        'port'     => '3306',
    ],
    'security' => [
        'password_algo'
    ],
    'mail' => [
        'driver'   => 'smtp',
        'host'     => 'smtp.gmail.com',
        'port'     => 587,
        'encryption' => 'tls',
        'username' => 'soporte.bibliopoas@gmail.com',
        'password' => 'dtxtnmnijzhxpzah',
        'from_email' => 'soporte.bibliopoas@gmail.com',
        'from_name'  => 'BiblioPoás - Soporte',
    ],
];

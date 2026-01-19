<?php
return [
  'db' => [
    'host' => '127.0.0.1',
    'database' => 'biblio_poas',
    'user' => 'root',
    'password' => 'root',
    'port' => '3306',
  ],
  'app' => [
    'base_url' => '/',
    'env'      => 'local',
  ],
  // === SMTP real para local
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

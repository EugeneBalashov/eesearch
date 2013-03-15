<?php
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbname' => 'dev',
        'host' => '127.0.0.1',
        'user' => 'test',
        'password' => 'test',
        'charset'   => 'utf8',
    ),
));
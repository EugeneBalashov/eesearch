<?php
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbname' => 'u742733526_dev',
        'host' => 'mysql.hostinger.com.ua',
        'user' => 'u742733526_dev',
        'password' => 'u742733526_dev',
        'charset'   => 'utf8',
    ),
));

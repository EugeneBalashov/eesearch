<?php
$controllers = $app['controllers_factory'];

$controllers->get('/', function () use ($app) {
    return $app['twig']->render('user/index.twig');
})->bind('user.index');

return $controllers;
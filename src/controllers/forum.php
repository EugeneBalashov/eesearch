<?php
use Silex\Application;

$forum = $app['controllers_factory'];
$forum->get('/', function() {
    return 'Forum Page';
});

$app->get('/forum/{id}', function (Application $app, $id) use ($app) {
    return $id;
});

return $forum;
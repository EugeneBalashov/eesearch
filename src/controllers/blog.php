<?php
use Silex\Application;

$blog = $app['controllers_factory'];
$blog->get('/', function() {
    return 'Blog Page';
});

$app->get('/blogs', function (Application $app) use ($app) {
    
    $token = $app['security']->getToken();
//
    if (null !== $token) {
        $user = $token->getUser();
    }
    var_dump($app->encodePassword($app->user(), 'foo'));
    
    $sql = "SELECT * FROM users";
    $users = $app['db']->fetchAll($sql);
    
    return $app['twig']->render('blog.twig', array(
        'users' => $users
    ));
});

$app->get('/blog/{id}', function (Application $app, $id) use ($app) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $users = $app['db']->fetchAssoc($sql, array((int) $id));

    return '<h1>blog/{id}</h1>';
});

return $blog;
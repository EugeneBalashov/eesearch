<?php
//use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
require_once 'UserProvider.php';

$app['security.user_provider'] = $app->share(function () use ($app) {
    return new UserProvider($app['db']);
});

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        
        'login' => array(
            'pattern' => '^/login$',
        ),
        
        /*'registration' => array(
            'pattern' => '^/registration$',
        ),*/
        
        'main' => array(
            'pattern'    => '^/',
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/login_check',
//                'failure_path' => '/login_failure'
            ),
            'logout' => array('logout_path' => '/logout'),
//            'anonymous'  => true,
//            'security' => true,
            'users' => $app['security.user_provider'],
//            'users' => $app->share(function () use ($app) {
//                return new UserProvider($app['db']);
//            }),
        ),
    ),
    
    'security.access_rules' => array(
        array('^/admin', 'ROLE_ADMIN'),
        array('^/user', 'ROLE_USER'),
    ),
                
    /*'encoder.digest' => $app->share(function ($app) {
        return new MessageDigestPasswordEncoder('sha1', false, 1);
    }),*/
    
));
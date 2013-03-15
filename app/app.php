<?php
require_once __DIR__.'/bootstrap.php';
 
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app = new Application();
 
//$app['debug'] = true;

require_once __DIR__.'/config/db.php';
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../src/views'));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array('translator.messages' => array()));
require_once __DIR__.'/config/security.php';
//--------------------------------------------------

$app['user'] = $app->share(function() use ($app) {
    $token = $app['security']->getToken();
    $user = $token->getUser();
    $user = $app['db']->fetchAssoc('SELECT * FROM users WHERE login = ?', array($user->getUsername()));
    return $user;
});


$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

$app->error(function (Exception $e, $code) use ($app) {
    if ($app['debug']) return;
    if ($e instanceof NotFoundHttpException) {
        return $app['twig']->render('error.twig', array('code' => 404));
    }
//    $code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;
    return $app['twig']->render('error.twig', array('code' => $code));
});

/*
$app->match('/registration', function(Request $request) use ($app) {
    $form = $app['form.factory']->createBuilder('form')
            ->add('login', 'text')
            ->add('password', 'password')
            ->getForm();
    
    if ('POST' == $request->getMethod()) {
        $form->bind($request);

        if ($form->isValid()) {
            $data = $form->getData();
            
            $UserModel = new \Application\UserModel($app);
            $UserModel->create($data);
            $User = new \Symfony\Component\Security\Core\User\User($data['username'], $data['password'], array('ROLE_USER'));
            $app['security']->setToken(new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($User, $User->getPassword(), 'user_firewall', array('ROLE_USER')));
            
            return $app->redirect('/');
        }
    }
    return $app['twig']->render('registration.twig', array('form' => $form->createView()));
})->bind('registration');
*/

$app->get('/', function () use ($app) {
    if ($app['security']->isGranted('ROLE_ADMIN')) {
        return $app->redirect('/admin');
    } else if ($app['security']->isGranted('ROLE_USER')) {
        return $app->redirect('/user');
    }
    return $app['twig']->render('login.twig');
})->bind('main');

$app->mount('/admin', require __DIR__.'/../src/controllers/admin.php');
$app->mount('/user', require __DIR__.'/../src/controllers/user.php');
//$app->mount('/test', new controllers\HelloController());

return $app;
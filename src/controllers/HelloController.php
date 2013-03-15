<?php
namespace controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;

class HelloController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return 'TEST';
        });

        return $controllers;
    }
}
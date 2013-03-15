<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

$controllers = $app['controllers_factory'];

$controllers->get('/', function () use ($app) {
    return $app['twig']->render('admin/index.twig');
})->bind('admin.index');

$controllers->get('/show/users', function () use ($app) {
    $users = $app['db']->fetchAll('SELECT users.id AS uid, roles.*, users.* FROM users, roles WHERE users.role = roles.id AND roles.role = "ROLE_USER"');
    return $app['twig']->render('admin/showUsers.twig', array(
        'users' => $users
    ));
})->bind('admin.show.users');

$controllers->match('/edit/user/{id}', function (Request $request, $id) use ($app) {
    $user = $app['db']->fetchAssoc('SELECT * FROM users, roles WHERE users.id = ? AND users.role = roles.id AND roles.role = "ROLE_USER"', array($id));
    
    if ($user) {
        $userPassword = $user['password'];
        $user['password'] = '';
        $form = $app['form.factory']->createBuilder('form', $user)
                ->add('login', 'text', array(
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5))),
                    'attr' => array('placeholder' => 'Login')
                ))
                ->add('password', 'text', array(
                    'constraints' => array(new Assert\Length(array('min' => 3))),
                    'required' => false,
                    'attr' => array('placeholder' => 'Change password'),
                ))
                ->add('name', 'text', array(
                    'constraints' => array(new Assert\NotBlank()),
                    'attr' => array('placeholder' => 'Name')
                ))
                ->getForm();

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $data['password'] = trim($data['password']);
                if (!empty($data['password'])) {
                    $porvidedUser = $app['security.user_provider']->loadUserByUsername($user['login']);
                    $encoder = $app['security.encoder_factory']->getEncoder($porvidedUser);
                    $data['password'] = $encoder->encodePassword($data['password'], $porvidedUser->getSalt());
                } else {
                    $data['password'] = $userPassword;
                }

                $app['db']->update('users', array(
                    'login' => $data['login'],
                    'password' => $data['password'],
                    'name' => $data['name']
                ), array('id' => $id));

                return $app->redirect($app['url_generator']->generate('admin.edit.user',  array('id' => $id)));
            }
        }
        return $app['twig']->render('admin/editUser.twig', array(
            'id' => $id,
            'form' => $form->createView()
        ));
    } else {
        return $app->redirect($app['url_generator']->generate('admin.show.users'));
    }
})->bind('admin.edit.user');


return $controllers;
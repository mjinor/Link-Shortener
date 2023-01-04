<?php
// Create Router instance
$router = new \Bramus\Router\Router();
$router->setNamespace('Mjinor\MjaShorturl\App\Controller');

$router->before('GET|POST','/api/url/.*',function () {
    \Mjinor\MjaShorturl\App\Model\User::currentUser();
});

// Defined routes
$router->mount('/api',function () use ($router) {

    $router->post('/register','AuthController@register');

    $router->post('/login','AuthController@login');

    $router->mount('/url',function () use ($router) {

        $router->get('/list','LinkController@fetchAll');

        $router->post('/create','LinkController@create');

        $router->post('/edit','LinkController@edit');

        $router->post('/delete','LinkController@delete');

    });

});

$router->get('/{hash}','LinkController@redirect');

// Run it!
$router->run();
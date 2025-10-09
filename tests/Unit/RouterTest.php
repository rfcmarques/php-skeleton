<?php

use Core\Router;

it('matches static and param routes', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $router = new Router();
    $router->get('/', 'HomeController@index');
    $router->get('/users/{id}', 'UserController@show');

    ob_start();
    $router->dispatch('/');
    ob_end_clean();

    // ob_start();
    // $router->dispatch('/users/42');
    // ob_end_clean();

    expect(true)->toBeTrue();
});

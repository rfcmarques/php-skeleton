<?php

use Core\Router;

it('dispatches via injected controller factory and passes params', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';

    // Spy ultra simples
    $calls = [];
    $mock = new class($calls) {
        public array $calls;
        public function __construct(&$calls)
        {
            $this->calls = &$calls;
        }
        public function show($params)
        {
            $this->calls[] = $params;
        }
    };

    $factory = function (string $fqcn) use ($mock) {
        return $mock;
    };

    $router = new Router($factory);
    $router->get('/users/{id}', 'UserController@show');

    ob_start();
    $router->dispatch('/users/99');
    ob_end_clean();

    expect($calls)->toHaveCount(1);
    expect($calls[0])->toMatchArray(['id' => '99']);
});

it('does not call factory when route does not match (returns 404)', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $factoryCalls = 0;
    $factory = function (string $fqcn) use (&$factoryCalls) {
        $factoryCalls++;

        return new class {
            public function show($params) {}
        };
    };

    $router = new Router($factory);
    $router->get('/users/{id}', 'UserController@show');

    ob_start();
    $router->dispatch('/nope');
    ob_end_clean();

    expect($factoryCalls)->toBe(0);
    expect(http_response_code())->toBe(404);
});

it('returns 404 when no route matches', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $router = new Router();

    ob_start();
    $router->dispatch('/nope');
    ob_end_clean();

    expect(http_response_code())->toBe(404);
});

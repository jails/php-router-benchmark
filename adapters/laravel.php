<?php
use Lead\Box\Box;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Routing\Router;
use Illuminate\Routing\Route;

$box = box('laravel', new Box());

$box->service('build-routes', function() {

    return function($generator, $options = []) {
        $placeholderTemplate = function($name, $pattern) {
            return "{{$name}}";
        };

        return $generator->generate($placeholderTemplate, null, $options);
    };
});

$box->service('add-routes', function() {

    return function($routes) {
        $dispatcher = new Dispatcher();
        $container = new Container();
        $router = new Router($dispatcher, $container);

        foreach ($routes as $route) {
            $action = [
                'uses' => function($id) { return $id; }
            ];
            if ($route['host'] !== "*") {
                $action['domain'] = $route['host'];
            }
            $router->getRoutes()->add(new Route($route['methods'], $route['pattern'], $action))->where($route['constraints']);
        }
        return $router;
    };
});

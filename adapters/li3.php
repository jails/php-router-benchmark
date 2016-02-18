<?php
use Lead\Box\Box;
use lithium\net\http\Router;

$box = box('li3', new Box());

$box->service('build-routes', function() {

    return function($generator, $options = []) {
        $placeholderTemplate = function($name, $pattern) {
            return "{{$name}:{$pattern}}";
        };

        return $generator->generate($placeholderTemplate, null, $options);
    };
});

$box->service('add-routes', function() {
    Router::reset();
    return function($routes) {
        foreach ($routes as $route) {
            foreach ($route['methods'] as $method) {
                if ($route['host'] === '*') {
                    Router::connect($route['pattern'], ['http:method' => $method]);
                } else {
                    Router::attach($route['host'], [
                        'absolute' => true,
                        'host' => $route['host']
                    ]);
                    Router::scope($route['host'], function() use ($route, $method) {
                        Router::connect($route['pattern'], ['http:method' => $method]);
                    });
                }
            }
        }
    };
});

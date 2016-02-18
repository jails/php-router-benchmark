<?php
use Lead\Box\Box;

$box = box('fast-route-classic', new Box());

$box->service('build-routes', function() {

    return function($generator, $options = []) {
        $placeholderTemplate = function($name, $pattern) {
            return "{{$name}:{$pattern}}";
        };

        $segmentTemplate = function($segment, $greedy) {
            return "[{$segment}]" . ($greedy !== '?' ? $greedy : '');
        };

        return $generator->generate($placeholderTemplate, $segmentTemplate, $options);
    };
});

$box->service('add-routes', function() {

    return function($routes) {
        $router = FastRoute\classicDispatcher(function($router) use ($routes) {
            foreach ($routes as $route) {
                foreach ($route['methods'] as $method) {
                    $router->addRoute($method, $route['pattern'], function() {});
                }
            }
        });
        return $router;
    };
});

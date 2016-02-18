<?php
use Lead\Box\Box;
use Lead\Router\Router;

$box = box('router', new Box());

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
        $router = new Router();
        foreach ($routes as $route) {
            $router->bind($route['pattern'], $route, function($route){});
        }
        return $router;
    };
});

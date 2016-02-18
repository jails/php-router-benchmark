<?php
use Lead\Box\Box;
use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;

$box = box('phroute', new Box());

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
        $collection = new RouteCollector();
        foreach ($routes as $route) {
            foreach ($route['methods'] as $method) {
                $collection->addRoute($method, $route['pattern'], function($id) {
                    return $id;
                });
            }
        }
        return new Dispatcher($collection->getData());
    };
});

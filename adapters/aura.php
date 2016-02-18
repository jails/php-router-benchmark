<?php
use Lead\Box\Box;
use Aura\Router\RouterContainer;

$box = box('aura', new Box());

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
        $routerContainer = new RouterContainer();
        $collection = $routerContainer->getMap();
        foreach ($routes as $route) {
            $auraRoute = $collection->route($route['name'], $route['pattern'],function() {});
            if ($route['host'] !== '*') {
                $auraRoute->host($route['host']);
            }
            $auraRoute->tokens($route['constraints']);
            $auraRoute->allows($route['methods']);
        }
        return $routerContainer->getMatcher();
    };
});

<?php
use Lead\Box\Box;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$box = box('symfony', new Box());

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
        $sfCollection = new RouteCollection();
        foreach ($routes as $route) {
            $sfRoute = new Route($route['pattern'], ['controller' => 'controller']);
            if ($route['host'] !== '*') {
                $sfRoute->setHost($route['host']);
            }
            $sfRoute->setMethods($route['methods']);
            $sfRoute->setRequirements($route['constraints']);
            $sfCollection->add($route['pattern'], $sfRoute);
        }
        return new UrlMatcher($sfCollection, new RequestContext());
    };
});

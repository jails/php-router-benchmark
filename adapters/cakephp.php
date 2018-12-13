<?php
use Lead\Box\Box;
use Cake\Routing\Router;

$ds = DIRECTORY_SEPARATOR;
define('CONFIG', dirname(__FILE__) . $ds . '..' . $ds . 'benchmarks' . $ds . 'cakephp' . $ds);

$box = box('CakePHP', new Box());

$box->service('build-routes', function() {
    return function($generator, $options = []) {
        $placeholderTemplate = function($name, $pattern) {
            return ":$name";
        };

        return $generator->generate($placeholderTemplate, null, $options);
    };
});

$box->service('add-routes', function() {
    return function($routes) {
        Router::setRouteCollection(new \Cake\Routing\RouteCollection());

        foreach ($routes as $route) {
            Router::connect($route['pattern']);
        }
    };
});

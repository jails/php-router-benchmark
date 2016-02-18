<?php
use Zend\Diactoros\ServerRequest;

$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}');

$buildRoutes = box('aura')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Aura3', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('aura')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->methods() as $method) {
        $id = $strategy($ids, $method);

        $request = new ServerRequest([], [], "/controller{$id}/action{$id}/{$id}/arg1/arg2", $method);
        $route = $router->match($request);
        $params = $route->attributes;

        if ($params['id'] !== (string) $id) {
            return false;
        }
    }

});

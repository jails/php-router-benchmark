<?php

$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}');

$buildRoutes = box('phroute')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('PHRoute', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('phroute')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->methods() as $method) {
        $id = $strategy($ids, $method);

        $result = $router->dispatch($method, "/controller{$id}/action{$id}/{$id}/arg1/arg2");

        if ($result !== (string) $id) {
            return false;
        }
    }

});

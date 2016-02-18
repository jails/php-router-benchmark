<?php

$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}');

$buildRoutes = box('router')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Router', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('router')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->methods() as $method) {
        $id = $strategy($ids, $method);

        $route = $router->route("/controller{$id}/action{$id}/{$id}/arg1/arg2", $method);
        $params = $route->params;

        if ($params['id'] !== (string) $id) {
            return false;
        }
    }

});

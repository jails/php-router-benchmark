<?php

$generator->host('subdomain{%hostId%}.domain.com', '*');
$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}');

$buildRoutes = box('router')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'nbHosts'  => box('benchmark')->get('nbHosts'),
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Router', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('router')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->hosts() as $host) {
        foreach ($generator->methods() as $method) {
            $id = $strategy($ids, $method, $host);

            $route = $router->route("/controller{$id}/action{$id}/{$id}/arg1/arg2", $method, $host);
            $params = $route->params;

            if ($params['id'] !== (string) $id) {
                return false;
            }
        }
    }

});

<?php

$generator->host('subdomain{%hostId%}.domain.com', '*');
$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}', [
    'id'   => '[^/]+',
    'arg1' => '[^/]+',
    'arg2' => '[^/]+'
]);

$buildRoutes = box('symfony')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'nbHosts'  => box('benchmark')->get('nbHosts'),
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Symfony', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('symfony')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->hosts() as $host) {
        foreach ($generator->methods() as $method) {
            $id = $strategy($ids, $method, $host);

            $router->getContext()->setHost($host);
            $router->getContext()->setMethod($method);
            $params = $router->match("/controller{$id}/action{$id}/{$id}/arg1/arg2");

            if ($params['id'] !== (string) $id) {
                return false;
            }
        }
    }
});

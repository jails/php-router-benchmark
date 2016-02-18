<?php
use lithium\net\http\Router;
use lithium\action\Request;

$generator->host('subdomain{%hostId%}.domain.com', '*');
$generator->template('/controller{%id%}/action{%id%}/{:id}/{:arg1}/{:arg2}');

$buildRoutes = box('li3')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'nbHosts'  => box('benchmark')->get('nbHosts'),
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Li3', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('li3')->get('add-routes');
    $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->hosts() as $host) {
        foreach ($generator->methods() as $method) {
            $id = $strategy($ids, $method, $host);

            $request = new Request([
                'url' => "/controller{$id}/action{$id}/{$id}/arg1/arg2",
                'env' => [
                    'HTTP_HOST'      => $host,
                    'REQUEST_METHOD' => $method
                ]
            ]);

            Router::parse($request);
            $params = $request->params;

            if ($params['id'] !== (string) $id) {
                return false;
            }
        }
    }

});

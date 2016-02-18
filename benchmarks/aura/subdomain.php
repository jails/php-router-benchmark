<?php
use Zend\Diactoros\Uri;
use Zend\Diactoros\ServerRequest;

$generator->host('subdomain{%hostId%}.domain.com', '*');
$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}');

$buildRoutes = box('aura')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'nbHosts'  => box('benchmark')->get('nbHosts'),
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Aura3', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('aura')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->hosts() as $host) {
        foreach ($generator->methods() as $method) {
            $id = $strategy($ids, $method, $host);

            $uri = new Uri("/controller{$id}/action{$id}/{$id}/arg1/arg2");
            $uri = $uri->withHost($host);
            $request = new ServerRequest([], [], $uri, $method);
            $route = $router->match($request);
            $params = $route->attributes;

            if ($params['id'] !== (string) $id) {
                return false;
            }
        }
    }

});

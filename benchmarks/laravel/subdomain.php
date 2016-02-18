<?php
use Illuminate\Http\Request;

$generator->host('subdomain{%hostId%}.domain.com', '*');
$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}', [
    'id'   => '[^/]+',
    'arg1' => '[^/]+',
    'arg2' => '[^/]+'
]);

$buildRoutes = box('laravel')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'nbHosts'  => box('benchmark')->get('nbHosts'),
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Laravel', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('laravel')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->hosts() as $host) {
        foreach ($generator->methods() as $method) {
            $id = $strategy($ids, $method, $host);

            $request = Request::create("/controller{$id}/action{$id}/{$id}/arg1/arg2", $method, [], [], [], [
                'HTTP_HOST' => $host
            ]);
            $result = $router->dispatch($request)->getContent() ;

            if ($result !== (string) $id) {
                return false;
            }
        }
    }
});

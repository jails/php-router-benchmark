<?php
use Illuminate\Http\Request;

$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}', [
    'id'   => '[^/]+',
    'arg1' => '[^/]+',
    'arg2' => '[^/]+'
]);

$buildRoutes = box('laravel')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('Laravel', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('laravel')->get('add-routes');
    $router = $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->methods() as $method) {
        $id = $strategy($ids, $method);

        $request = Request::create("/controller{$id}/action{$id}/{$id}/arg1/arg2", $method);
        $result = $router->dispatch($request)->getContent() ;

        if ($result !== (string) $id) {
            return false;
        }
    }
});

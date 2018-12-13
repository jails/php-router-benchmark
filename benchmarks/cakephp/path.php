<?php
use Cake\Routing\Router;
use Zend\Diactoros\ServerRequest;

$generator->template('/controller{%id%}/action{%id%}/{id}/{arg1}/{arg2}');

$buildRoutes = box('CakePHP')->get('build-routes');

list($ids, $routes) = $buildRoutes($generator, [
    'isolated' => box('benchmark')->get('isolated')
]);

$benchmark->run('CakePHP', function() use ($box, $generator, $ids, $routes) {

    $addRoutes = box('CakePHP')->get('add-routes');
    $addRoutes($routes);

    $strategy = box('benchmark')->get('strategy');

    foreach ($generator->methods() as $method) {
        $id = $strategy($ids, $method);

        $request = new ServerRequest([], [], "/controller{$id}/action{$id}/{$id}/arg1/arg2", $method);
        $result = Router::parseRequest($request);

        if ($result === false) {
            return false;
        }
    }
});

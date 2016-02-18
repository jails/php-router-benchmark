#!/usr/bin/env php
<?php
$composer = include './vendor/autoload.php';
include 'RouteGenerator.php';

$composer->add('lithium\\', __DIR__ . '/libraries');

use Lead\Box\Box;
use Lead\Dir\Dir;
use Lead\Benchmark\Benchmark;

$adapters = Dir::scan('./adapters', [
    'type'     => 'file',
    'skipDots' => true
]);

foreach ($adapters as $file) {
    if (file_exists($file)) {
        include $file;
    }
}

$box = box('benchmark', new Box());

$modes = [
    'With Routes Supporting All HTTP Methods' => false,
    'With Routes Supporting Only A Single HTTP Methods' => true
];

$benchmarks = [
    [
        'filename'   => 'path',
        'nbRoutes' => 100,
        'forms' => [
            'Best Case (path)' => [
                'strategy' => function() {
                    return function(&$ids, &$method, $host = '*') {
                        return reset($ids[$host][$method]);
                    };
                }
            ],
            'Average Case (path)' => [
                'strategy' => function() {
                    return function(&$ids, &$method, $host = '*') {
                        return $ids[$host][$method][floor(count($ids[$host][$method]) / 2)];
                    };
                }
            ],
            'Worst Case (path)' => [
                'strategy' => function() {
                    return function(&$ids, &$method, $host = '*') {
                        return end($ids[$host][$method]);
                    };
                }
            ]
        ]
    ],
    [
        'filename' => 'subdomain',
        'nbRoutes' => 10,
        'forms' => [
            'Best Case (sub-domain)' => [
                'nbHosts'  => function() {
                    return 10;
                },
                'strategy' => function() {
                    return function(&$ids, &$method, $host) {
                        return reset($ids[$host][$method]);
                    };
                }
            ],
            'Average Case (sub-domain)' => [
                'nbHosts'  => function() {
                    return 10;
                },
                'strategy' => function() {
                    return function(&$ids, &$method, $host) {
                        return $ids[$host][$method][floor(count($ids[$host][$method]) / 2)];
                    };
                }
            ],
            'Worst Case (sub-domain)' => [
                'nbHosts'  => function() {
                    return 10;
                },
                'strategy' => function() {
                    return function(&$ids, &$method, $host) {
                        return end($ids[$host][$method]);
                    };
                }
            ]
        ]
    ]
];

$dirs = Dir::scan('./benchmarks', [
    'type'     => 'dir',
    'skipDots' => true
]);

foreach ($modes as $title => $isolated) {
    echo Benchmark::title($title, '#');
    $box->service('isolated', $isolated);

    foreach ($benchmarks as $bench) {
        $name = $bench['filename'];

        foreach ($bench['forms'] as $title => $variables) {

            foreach ($variables as $key => $value) {
                $box->service($key, $value);
            }

            $benchmark = new Benchmark();
            echo Benchmark::title($title);
            $benchmark->repeat(10);
            foreach ($dirs as $dir) {
                $path = $dir . DIRECTORY_SEPARATOR . $name . ".php";

                if (file_exists($path)) {
                    $generator = new RouteGenerator();
                    $generator->nbRoutes($bench['nbRoutes']);
                    include $path;
                }
                gc_collect_cycles();
            }
            echo $benchmark->chart();
        }
    }
}

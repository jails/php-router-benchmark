# PHP Router Benchmark

[![Build Status](https://travis-ci.org/jails/php-router-benchmark.svg?branch=master)](https://travis-ci.org/jails/php-router-benchmark)

The intent here is to benchmark and also inventory all popular PHP routing solutions around.

## Requirements

php = 7.3

## Installation

Clone the repo then:

```bash
composer install
./benchmark.php
```

## Benchmarking process

The current test creates 100 unique routes with 3 variables placeholder each.

Example of route: `/controller1/action1/{id}/{arg1}/{arg2}`

This benchmarking will be run on the following three different situations for both path & subdomain:
* the best case (i.e when a request matches the first route for all differents HTTP method)
* the worst case (i.e when a request matches the last route for all differents HTTP method)
* the average case (i.e the mean which is probably the most realistic test).

And all tests will be run using the following sets of routes:
* in the first set all routes matches all HTTP methods.
* in the second set all routes matches only a single HTTP method.

The benchmarked routing implementations are:

* [Router](https://github.com/crysalead/router)
* [Li3](https://github.com/UnionOfRAD/lithium)
* [FastRoute](https://github.com/nikic/FastRoute)
* [FastRoute\*](https://github.com/jails/FastRoute) = FastRoute + [a classic routing strategy](https://github.com/jails/FastRoute/commit/114676515b636b637f6cac53945c2e04875b60eb)
* [Symfony](https://github.com/symfony/routing)
* [Aura3](https://github.com/auraphp/Aura.Router)
* [PHRoute](https://github.com/mrjgreen/phroute)

## Results

```
################### With Routes Supporting All HTTP Methods ###################



=============================== Best Case (path) ===============================

Symfony         100% | ████████████████████████████████████████████████████████████  |
Aura3            79% | ███████████████████████████████████████████████               |
Router           73% | ███████████████████████████████████████████                   |
Laravel          23% | █████████████                                                 |
FastRoute        12% | ███████                                                       |
FastRoute*       12% | ███████                                                       |
PHRoute           8% | ████                                                          |
Li3               5% | ███                                                           |


============================= Average Case (path) =============================

Symfony         100% | ████████████████████████████████████████████████████████████  |
Router           85% | ██████████████████████████████████████████████████            |
Aura3            36% | █████████████████████                                         |
FastRoute*       22% | █████████████                                                 |
FastRoute        21% | ████████████                                                  |
Laravel          16% | █████████                                                     |
PHRoute          15% | ████████                                                      |
Li3              10% | █████                                                         |


============================== Worst Case (path) ==============================

Symfony         100% | ████████████████████████████████████████████████████████████  |
Router           77% | █████████████████████████████████████████████                 |
FastRoute*       34% | ████████████████████                                          |
FastRoute        32% | ███████████████████                                           |
Aura3            30% | ██████████████████                                            |
PHRoute          23% | █████████████                                                 |
Laravel          14% | ████████                                                      |
Li3              13% | ███████                                                       |


============================ Best Case (sub-domain) ============================

Router       100% | ████████████████████████████████████████████████████████████  |
Symfony       50% | ██████████████████████████████                                |
Aura3         11% | ██████                                                        |
Li3            5% | ███                                                           |
Laravel        2% | █                                                             |


========================== Average Case (sub-domain) ==========================

Router       100% | ████████████████████████████████████████████████████████████  |
Symfony       78% | ██████████████████████████████████████████████                |
Aura3         15% | ████████                                                      |
Li3            9% | █████                                                         |
Laravel        3% | █                                                             |


=========================== Worst Case (sub-domain) ===========================

Symfony      100% | ████████████████████████████████████████████████████████████  |
Router        92% | ███████████████████████████████████████████████████████       |
Aura3         17% | ██████████                                                    |
Li3           11% | ██████                                                        |
Laravel        4% | ██                                                            |


############## With Routes Supporting Only A Single HTTP Methods ##############



=============================== Best Case (path) ===============================

Aura3           100% | ████████████████████████████████████████████████████████████  |
Symfony          97% | █████████████████████████████████████████████████████████     |
Router           81% | ████████████████████████████████████████████████              |
FastRoute*       51% | ██████████████████████████████                                |
FastRoute        46% | ███████████████████████████                                   |
Laravel          39% | ███████████████████████                                       |
PHRoute          31% | ██████████████████                                            |
Li3              24% | ██████████████                                                |


============================= Average Case (path) =============================

FastRoute*      100% | ████████████████████████████████████████████████████████████  |
Router           95% | █████████████████████████████████████████████████████████     |
FastRoute        93% | ████████████████████████████████████████████████████████      |
Symfony          89% | █████████████████████████████████████████████████████         |
PHRoute          62% | █████████████████████████████████████                         |
Laravel          41% | ████████████████████████                                      |
Aura3            33% | ███████████████████                                           |
Li3              31% | ██████████████████                                            |


============================== Worst Case (path) ==============================

FastRoute*      100% | ████████████████████████████████████████████████████████████  |
FastRoute        94% | ████████████████████████████████████████████████████████      |
PHRoute          62% | ████████████████████████████████████                          |
Symfony          61% | ████████████████████████████████████                          |
Router           56% | █████████████████████████████████                             |
Laravel          28% | ████████████████                                              |
Li3              24% | ██████████████                                                |
Aura3            19% | ███████████                                                   |


============================ Best Case (sub-domain) ============================

Router       100% | ████████████████████████████████████████████████████████████  |
Symfony       55% | █████████████████████████████████                             |
Aura3         12% | ███████                                                       |
Li3           12% | ██████                                                        |
Laravel        8% | ████                                                          |


========================== Average Case (sub-domain) ==========================

Router       100% | ████████████████████████████████████████████████████████████  |
Symfony       80% | ████████████████████████████████████████████████              |
Li3           17% | ██████████                                                    |
Aura3         15% | █████████                                                     |
Laravel       11% | ██████                                                        |


=========================== Worst Case (sub-domain) ===========================

Router       100% | ████████████████████████████████████████████████████████████  |
Symfony       79% | ███████████████████████████████████████████████               |
Li3           16% | █████████                                                     |
Aura3         15% | █████████                                                     |
Laravel       11% | ██████                                                        |
```

<?php
require_once 'vendor/autoload.php';

require_once 'functions.php';

use Bandit\Ucb1;

use Bandit\UcbTuned;

/**
 * Снимаем текущий показатель памяти
 */
$memory = memory_get_usage();


ini_set('memory_limit', -1);


/**
 * Текущий показатель времени
 */
$start = microtime(true);


$ar = [
    'a' => [0, 0],
    'b' => [1, 0],
    'c' => [1, 1],
    'd' => [10, 2]
];

$ucb = new UcbTuned($ar);

print_r(($ucb->predict(true)));

// return
// Array
// (
//     [c] => 1.2364538001814
//     [d] => 0.27477325699756
//     [a] => 0.23645380018141
//     [b] => 0.23645380018141
// )

/**
 * Снимаем показатели и выводит результат
 */
result(memory_get_usage() - $memory, microtime(true) - $start);


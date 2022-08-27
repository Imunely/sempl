<?php
require_once 'vendor/autoload.php';

require_once 'functions.php';

use Bandit\Ucb1;

use Bandit\UcbTuned;
use Bandit\Wilson;

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
    'a' => [1000, 400],
    'b' => [500, 300],
    'c' => [10, 1],
    'd' => [2, 1]
];

$ucb = new Wilson($ar);

print_r(($ucb->predict(true)));

// return
// Array
// (
//     [b] => 0.56452204974786
//     [a] => 0.3754986027071
//     [d] => 0.12536567536732
//     [c] => 0.023443647821773
// )

/**
 * Снимаем показатели и выводит результат
 */
result(memory_get_usage() - $memory, microtime(true) - $start);


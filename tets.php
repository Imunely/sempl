<?php

/**
 * Снимаем текущий показатель памяти
 */
$memory = memory_get_usage();

require_once 'ThomasFullFeature.php';
require_once 'functions.php';

ini_set('memory_limit', -1);


// $count = [
//     '100K' => 100000, '300K' => 300000,
//     '600K' => 600000, '1M' => 1000000
// ];

// /**
//  * Генерируем случайную выборку
//  */
// for ($i = 0; $i < $count['100K']; $i++) {
//     $records[] = [rand(10000, 1000000), rand(0, 10000)];
// }

/**
 * Текущий показатель времени
 */
$start = microtime(true);

$ar = getcsv();

$ar[0] = [1000, 800];

$thompson = new ThompsonSempl($ar);
print_r(array_slice($thompson->predict(true), 0, 40, true));


/**
 * Снимаем показатели и выводит результат
 */
result(memory_get_usage() - $memory, microtime(true) - $start);

/**
 * for 100K: 0.1958 сек. / 39.87 МБ
 * 
 * for 300K: 0.5983 сек. / 123.59 МБ
 * 
 * for 600K: 1.1853 сек. / 247.16 МБ
 * 
 * for 1M: 1.95 сек. / 390.6 МБ
 */

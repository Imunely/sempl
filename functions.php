<?php

function result($memory, $time)
{
    $i = 0;
    while (floor($memory / 1024) > 0) {
        $i++;
        $memory /= 1024;
    }

    $name = array('байт', 'КБ', 'МБ');

    $memory = round($memory, 2) . ' ' . $name[$i];

    echo round($time, 4) . ' сек. / ' . $memory;
}

function getcsv()
{
    $first = true;
    $out = [];
    if (($handle = fopen("/Users/emmanyel/Downloads/sampling.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
            if ($first) {
                $first = false;
                continue;
            }
            $out[$data[0]] = [$data[1], $data[2]];
        }
        fclose($handle);
    }

    return $out;
}

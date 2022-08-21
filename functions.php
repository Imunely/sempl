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

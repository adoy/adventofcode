<?php

declare(strict_types=1);

const UP = [ -1, 0 ];
const RIGHT = [ 0, 1 ];
const DOWN = [ 1, 0 ];
const LEFT = [ 0, -1 ];

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$sum1 = $sum2 = 0;
$length1 = $length2 = 0;
$current1 = $current2 = $last1 = $last2 = [ 0, 0 ];

foreach ($lines as $line) {
    preg_match('/([LRDU]) (\d+) \(#([0-9a-f]+)\)/', $line, $matches);
    $steps1 = $matches[2];
    $steps2 = hexdec(substr($matches[3], 0, 5));
    $direction1 = match ($matches[1]) {
        'U' => UP,
        'R' => RIGHT,
        'D' => DOWN,
        'L' => LEFT,
    };
    $direction2 = match ($matches[3][5]) {
        '0' => RIGHT,
        '1' => DOWN,
        '2' => LEFT,
        '3' => UP,
    };
    $current1 = [ $current1[0] + $direction1[0] * $steps1, $current1[1] + $direction1[1] * $steps1 ];
    $sum1 += -($current1[1] - $last1[1]) * $last1[0];
    $length1+=$steps1;
    $last1 = $current1;

    $current2 = [ $current2[0] + $direction2[0] * $steps2, $current2[1] + $direction2[1] * $steps2 ];
    $sum2 += -($current2[1] - $last2[1]) * $last2[0];
    $length2+=$steps2;
    $last2 = $current2;
}
$sum1 = $sum1 - $length1 / 2 + 1;
$sum2 = $sum2 - $length2 / 2 + 1;

echo 'Result 1: ', $sum1 + $length1, PHP_EOL;
echo 'Result 2: ', $sum2 + $length2, PHP_EOL;

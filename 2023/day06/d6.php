<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

[ $time, $distance ] = $input;
[ , $times ] = explode(':', $time);
$times = array_values(array_map('intval', array_filter(explode(' ', $times))));
[ , $distances ] = explode(':', $distance);
$distances = array_values(array_map('intval', array_filter(explode(' ', $distances))));

function foo($time, $record) {
    for ($i = 0; $i <= $time; $i++) {
        $speed = $i;
        $distance = $speed * ( $time - $speed );
        if ($distance > $record) {
            $start = $speed;
            break;
        }
    }
    for ($i = $time; $i > $start ; $i--) {
        $speed = $i;
        $distance = $speed * ( $time - $speed );
        if ($distance > $record) {
            $end = $speed;
            break;
        }
    }

    return $end - $start + 1;
}

$result1 = 1;
foreach ($times as $race => $time) {
    $result1 *= foo($time, $distances[$race]);
}

$result2 = foo((int) implode($times), (int) implode($distances));

echo 'Result 1: ' . $result1 . PHP_EOL;
echo 'Result 2: ' . $result2 . PHP_EOL;

<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$width = strlen($lines[0]);
$height = count($lines);

$start = [ 0, 1 ];
$visited = [];

$queue = new SplQueue();
$queue->enqueue([ ...$start, 0, $visited ]);
$result1 = 0;
while (!$queue->isEmpty()) {
    [ $y, $x, $steps, $visited ] = $queue->dequeue();
    if ($y === $height - 1 && $x === $width - 2) {
        $result1 = max($result1, $steps);
    }
    $visited[$y][$x] = true;
    $possibleNext = match ($lines[$y][$x]) {
        '>' => [ [ 0, 1 ] ],
        '<' => [ [ 0, -1 ] ],
        '^' => [ [ -1, 0 ] ],
        'v' => [ [ 1, 0 ] ],
        default => [ [ 0, 1 ], [ 0, -1 ], [ 1, 0 ], [ -1, 0 ] ],
    };
    foreach ($possibleNext as [ $dy, $dx ]) {
        $ny = $y + $dy;
        $nx = $x + $dx;
        if ($ny < 0 || $ny >= $height || $nx < 0 || $nx >= $width) {
            continue;
        }
        if ('#' === $lines[$ny][$nx]) {
            continue;
        }
        if (isset($visited[$ny][$nx])) {
            continue;
        }
        $queue->enqueue([ $ny, $nx, $steps + 1, $visited ]);
    }
}

echo 'Result 1: ', $result1, PHP_EOL;

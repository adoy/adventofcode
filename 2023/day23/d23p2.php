<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$width = strlen($lines[0]);
$height = count($lines);

$intersections = [];
foreach ($lines as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        if ('#' === $char) {
            continue;
        }
        $possibleDirections = 0;
        foreach ([ [0, 1], [0, -1], [1, 0], [-1, 0] ] as [ $dy, $dx ]) {
            $ny = $y + $dy;
            $nx = $x + $dx;
            if ($nx < 0 || $nx >= $width || $ny < 0 || $ny >= $height) {
                continue;
            }
            if ('#' !== $lines[$ny][$nx]) {
                ++$possibleDirections;
            }
        }
        if ($possibleDirections > 2) {
            $intersections[] = [ $y, $x ];
        }
    }
}

$pointsOfInterest = [...$intersections, [0, 1], [$height-1, $width - 2]];
$neighbours = [];
foreach ($pointsOfInterest as [ $oy, $ox ]) {
    $queue = new SplQueue();
    $queue->enqueue([ $oy, $ox, 0, [] ]);
    $neighbours[$oy][$ox] = [];

    while (!$queue->isEmpty()) {
        [ $y, $x, $steps, $visited ] = $queue->dequeue();
        if (($y !== $oy || $x !== $ox) && in_array([$y, $x], $pointsOfInterest)) {
            $neighbours[$oy][$ox][] = [ $y, $x, $steps ];
            continue;
        }
        $visited[$y][$x] = true;
        $possibleNext = match ($lines[$y][$x]) {
            default => [ [ 0, 1 ], [ 0, -1 ], [ 1, 0 ], [ -1, 0 ] ],
        };
        foreach ($possibleNext as [ $dy, $dx ]) {
            $ny = $y + $dy;
            $nx = $x + $dx;
            if ($nx < 0 || $nx >= $width || $ny < 0 || $ny >= $height) {
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
}

$queue = new SplQueue();
$queue->enqueue([ 0, 1, 0, [] ]);
$result2 = 0;
while (!$queue->isEmpty()) {
    [ $y, $x, $steps, $visited ] = $queue->dequeue();
    if ($y === $height - 1 && $x === $width - 2) {
        if ($result2 < $steps) {
            $result2 = $steps;
        }
        continue;
    }
    $visited[$y][$x] = true;
    foreach ($neighbours[$y][$x] ?? [] as [ $ny, $nx, $nsteps ]) {
        if (isset($visited[$ny][$nx])) {
            continue;
        }
        $queue->enqueue([ $ny, $nx, $steps + $nsteps, $visited ]);
    }
}

echo 'Result 2: ', $result2, PHP_EOL;

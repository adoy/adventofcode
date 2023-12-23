<?php

declare(strict_types=1);

function brickDirection(array $brick): array
{
    $directions = [];
    for ($i = 0; $i < 3; ++$i) {
        $directions[] = $brick[$i] === $brick[$i + 3] ? 0 : ($brick[$i] < $brick[$i + 3] ? 1 : -1);
    }

    return $directions;
}

function brickLowestPoint(array $brick): int
{
    return min($brick[2], $brick[5]);
}

function brickPoints(array $brick): iterable
{
    $points = [];
    $d = brickDirection($brick);
    yield [ $x, $y, $z ] = [ $brick[0], $brick[1], $brick[2] ];
    do {
        yield [ $x += $d[0], $y += $d[1], $z += $d[2] ];
    } while ($x !== $brick[3] || $y !== $brick[4] || $z !== $brick[5]);
}

function getOverlappingBricks(array $grid, $brick): array
{
    $contacts = [];
    foreach (brickPoints($brick) as [$x, $y, $z]) {
        if (isset($grid[$x][$y][$z])) {
            $contacts[] = $grid[$x][$y][$z];
        }
    }

    return array_unique($contacts);
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$bricks = [];
$index = 0;
foreach ($lines as $line) {
    if (!preg_match('/(\d+),(\d+),(\d+)~(\d+),(\d+),(\d+)/', $line, $matches)) {
        break;
    }
    [, $x1, $y1, $z1, $x2, $y2, $z2] = array_map(intval(...), $matches);
    $bricks[$index++] = [ $x1, $y1, $z1, $x2, $y2, $z2 ];
}

uasort($bricks, function ($a, $b) {
    return brickLowestPoint($a) <=> brickLowestPoint($b);
});

$grid = $loadBearingBricks = $support = $supportedBy = [];
foreach ($bricks as $i => &$brick) {
    while (1 !== brickLowestPoint($brick)) {
        $brickDown = [ $brick[0], $brick[1], $brick[2] - 1, $brick[3], $brick[4], $brick[5] - 1 ];
        if ($overlap = getOverlappingBricks($grid, $brickDown)) {
            foreach ($overlap as $o) {
                $support[$o][] = $i;
                $supportedBy[$i][] = $o;
            }
            if (1 === count($overlap)) {
                $loadBearingBricks[$overlap[0]] = true;
            }
            break;
        }
        $brick = $brickDown;
    }

    foreach (brickPoints($brick) as [$x, $y, $z]) {
        $grid[$x][$y][$z] = $i;
    }
}

$result1 = count($bricks) - count($loadBearingBricks);

$result2 = 0;
foreach (array_keys($loadBearingBricks) as $loadBearingBrick) {
    $queue = new SplQueue();
    $queue->enqueue($loadBearingBrick);
    $removed = [];
    while (!$queue->isEmpty()) {
        $brick = $queue->dequeue();
        $removed[] = $brick;
        foreach ($support[$brick] ?? [] as $supportedBrick) {
            if (!array_diff($supportedBy[$supportedBrick], $removed)) {
                $queue->enqueue($supportedBrick);
            }
        }
    }
    $result2 += (count($removed)-1);
}

echo 'Result 1: ', $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

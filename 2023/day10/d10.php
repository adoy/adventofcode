<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

const TOP = 1 << 0;
const RIGHT = 1 << 1;
const BOTTOM = 1 << 2;
const LEFT = 1 << 3;
const VISITED = 1 << 4;

const DIRECTIONS = [
    TOP => [ -1, 0, BOTTOM ],
    RIGHT => [ 0, 1, LEFT ],
    BOTTOM => [ 1, 0, TOP ],
    LEFT => [ 0, -1, RIGHT ],
];

foreach ($lines  as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        if ('S' === $char) {
            $start = [$x, $y];
        }
        $originalMap[$y][$x] = match ($char) {
            'S' => TOP | RIGHT | LEFT | BOTTOM,
            'F' => RIGHT | BOTTOM,
            'L' => TOP | RIGHT,
            '7' => BOTTOM | LEFT,
            'J' => TOP | LEFT,
            '|' => TOP | BOTTOM,
            '-' => LEFT | RIGHT,
            default => 0,
        };
    }
}

[ $x, $y ] = $start;
foreach (array_keys(DIRECTIONS) as $initialDirection) {
    $map = $originalMap;
    $direction = $initialDirection;
    $length = 0;
    while (true) {
        if ($direction & (TOP | RIGHT | BOTTOM | LEFT)) {
            [ $offsetY, $offsetX, $oppositDirection ] = DIRECTIONS[$direction];
            $newCell = $map[$newY = $y + $offsetY][$newX = $x + $offsetX] ?? 0;
            if ($oppositDirection & $newCell) {
                ++$length;
                if ($start === [$newX, $newY]) {
                    $map[$start[1]][$start[0]] = $initialDirection | $oppositDirection | VISITED;
                    break 2;
                }
                [ $x, $y, $direction ] = [ $newX, $newY, $oppositDirection ^ $newCell ];
                $map[$y][$x] |= VISITED;
                continue;
            }
            break;
        }
    }
}

$result1 = $length/2;

$width = count($map[0]);
$height = count($map);
$printMap = max($width, $height) < 50;

$result2 = 0;
foreach ($map as $y => $line) {
    $inside = false;
    foreach ($line as $x => $cell) {
        if ($printMap) pc($cell);
        if ($cell & VISITED) {
            if ($cell & TOP) {
                $inside = !$inside;
            }
            continue;
        }
        $result2 += $inside ? 1 : 0;
    }
    if ($printMap) echo PHP_EOL;
}

function pc(int $pc)
{
    echo match ($pc) {
        TOP | BOTTOM => '|',
        TOP | BOTTOM | VISITED => '┃',
        LEFT | RIGHT => '-',
        LEFT | RIGHT | VISITED => '━',
        TOP | RIGHT => '└',
        TOP | RIGHT | VISITED => '┗',
        TOP | LEFT => '┘',
        TOP | LEFT | VISITED => '┛',
        BOTTOM | RIGHT => '┌',
        BOTTOM | RIGHT | VISITED => '┏',
        BOTTOM | LEFT => '┐',
        BOTTOM | LEFT | VISITED => '┓',
        default => ' ',
    };
}

echo 'Result 1: ' . $result1 . PHP_EOL;
echo 'Result 2: ' . $result2 . PHP_EOL;

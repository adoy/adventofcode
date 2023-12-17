<?php

declare(strict_types=1);

function solve(array $lines, int $part): int
{
    $width = strlen($lines[0]);
    $height = count($lines);

    $queue = new class () extends SplHeap {
        public function compare(mixed $a, mixed $b): int
        {
            return $b[0] <=> $a[0];
        }
    };

    $visited = [];
    $queue->insert([0, 0, 0, 0, 0]);
    while (!$queue->isEmpty()) {
        [$heat, $y, $x, $direction, $dcount] = $queue->extract();

        if (isset($visited[$y][$x][$direction][$dcount])) {
            continue;
        }
        $visited[$y][$x][$direction][$dcount] = true;

        if ($y === $height - 1 && $x === $width - 1 && (1 === $part || $dcount >= 4)) {
            return $heat;
        }

        foreach ([
            [$y + 1, $x],
            [$y, $x + 1],
            [$y - 1, $x],
            [$y, $x - 1],
        ] as $newDirection => [$newY, $newX]) {
            if ($newY < 0 || $newX < 0 || $newY >= $height || $newX >= $width) {
                continue;
            }
            $newDcount = ($direction === $newDirection) ? $dcount + 1 : 1;

            if (1 === $part && (
                ($newDcount > 3) ||
                (($newDirection + 2) % 4 === $direction))
            ) {
                continue;
            } elseif (2 === $part && (
                ($newDcount > 10) ||
                ($dcount && $newDirection !== $direction && $dcount < 4))
            ) {
                continue;
            }

            $newHeat = $lines[$newY][$newX] ?? null;

            $queue->insert([$heat + $newHeat, $newY, $newX, $newDirection, $newDcount]);
        }
    }
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

echo 'Result 1: ', solve($lines, 1), PHP_EOL;
echo 'Result 2: ', solve($lines, 2), PHP_EOL;

<?php

declare(strict_types=1);

function findSplit($grid, int $ignore = null) {
    $height = count($grid);
    $width = strlen($grid[0]);

    // Horizontal split
    for ($y = 1; $y < $height; $y++) {
        if ($y * 100 === $ignore) {
            continue;
        }
        $y1 = $y - 1;
        $y2 = $y;
        while ($grid[$y1--] === $grid[$y2++]) {
            if ($y1 < 0 || $y2 >= $height) {
                return $y * 100;
            }
        }
    }

    $rgrid = [];
    for ($x = 0; $x < $width; $x++) {
        $rgrid[$x] = '';
        for ($y = 0; $y < $height; $y++) {
            $rgrid[$x] .= $grid[$y][$x];
        }
    }

    // Vertical split
    for ($x = 1; $x < $width; $x++) {
        if ($x === $ignore) {
            continue;
        }
        $x1 = $x - 1;
        $x2 = $x;
        while ($rgrid[$x1--] === $rgrid[$x2++]) {
            if ($x1 < 0 || $x2 >= $width) {
                return $x;
            }
        }
    }

    return null;
}

function permute(array $grid): iterable
{
    $height = count($grid);
    $width = strlen($grid[0]);
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $newGrid = $grid;
            $newGrid[$y][$x] = $grid[$y][$x] === '.' ? '#' : '.';
            yield $newGrid;
        }
    }
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);
$lines[] = '';

$result1 = $result2 = 0;
$grid = [];
foreach ($lines as $line) {
    if (empty($line)) {
        $result1 += $oldSplit = findSplit($grid);
        foreach (permute($grid) as $n => $newGrid) {
            $newSplit = findSplit($newGrid, $oldSplit);
            if (null !== $newSplit) {
                $result2 += ($newSplit);
                break;
            }
        }
        $grid = [];
        continue;
    }
    $grid[] = $line;
}

echo 'Result 1: ',  $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

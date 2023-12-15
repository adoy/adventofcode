<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

function solve(array $lines, array $directions, int $n = 1): int
{
    $width = strlen($lines[0]);
    $height = count($lines);

    $cache = [];
    $loop  = [];

    for ($i = 0; $i < $n; ++$i) {
        $key = implode('', $lines);
        if (isset($loop[$key])) {
            $istart = $loop[$key];
            $iend = $i;
            break;
        }
        $loop[$key] = $i;
        $cache[$i] = $lines;

        foreach ($directions as $direction) {
            switch ($direction) {
                case 'N':
                    for ($x = 0; $x < $width; ++$x) {
                        $pos = 0;
                        for ($y = 0; $y < $height; ++$y) {
                            switch ($lines[$y][$x]) {
                                case 'O':
                                    $lines[$y][$x] = '.';
                                    $lines[$pos++][$x] = 'O';
                                    break;
                                case '#':
                                    $pos = $y+1;
                                    break;
                            }
                        }
                    }
                    break;
                case 'S':
                    for ($x = 0; $x < $width; ++$x) {
                        $pos = $height - 1;
                        for ($y = $height - 1; $y >= 0; --$y) {
                            switch ($lines[$y][$x]) {
                                case 'O':
                                    $lines[$y][$x] = '.';
                                    $lines[$pos--][$x] = 'O';
                                    break;
                                case '#':
                                    $pos = $y-1;
                                    break;
                            }
                        }
                    }
                    break;
                case 'E':
                    for ($y = 0; $y < $height; ++$y) {
                        $pos = $width - 1;
                        for ($x = $width - 1; $x >= 0; --$x) {
                            switch ($lines[$y][$x]) {
                                case 'O':
                                    $lines[$y][$x] = '.';
                                    $lines[$y][$pos--] = 'O';
                                    break;
                                case '#':
                                    $pos = $x-1;
                                    break;
                            }
                        }
                    }
                    break;
                case 'W':
                    for ($y = 0; $y < $height; ++$y) {
                        $pos = 0;
                        for ($x = 0; $x < $width; ++$x) {
                            switch ($lines[$y][$x]) {
                                case 'O':
                                    $lines[$y][$x] = '.';
                                    $lines[$y][$pos++] = 'O';
                                    break;
                                case '#':
                                    $pos = $x+1;
                                    break;
                            }
                        }
                    }
                    break;
            }
        }
    }

    if ($i !== $n) {
        $remaining = $n;
        $remaining -= $istart;
        $remaining %= ($iend - $istart);
        $lines = $cache[$istart + $remaining];
    }

    $result = 0;
    foreach ($lines as $y => $line) {
        $result += ($height - $y) * substr_count($line, 'O');
    }

    return $result;
}

echo 'Result 1: ', solve($lines, [ 'N' ]), PHP_EOL;
echo 'Result 2: ',  solve($lines, [ 'N', 'W', 'S', 'E' ], 1000000000), PHP_EOL;

<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

$instructions = array_shift($input);
array_shift($input);

$maps = [];
$keys = [];
foreach ($input as $line) {
    preg_match('/([0-9A-Za-z]+) = \(([0-9A-Za-z]+), ([0-9A-Za-z]+)/', $line, $matches);
    $maps[$matches[1]] = [$matches[2], $matches[3]];
    if ('A' === $matches[1][2]) {
        $keys[] = $matches[1];
    }
}

function part1(string $instructions, array $maps)
{
    $iLength = strlen($instructions);
    $key = 'AAA';
    $count = 0;
    while ($key && 'ZZZ' !== $key) {
        $dir = $instructions[$count++ % $iLength];
        $key = $maps[$key]['R' === $dir ? 1 : 0] ?? null;
    }

    return $key ? $count : 'Invalid input';
}

function part2(string $instructions, array $maps, array $keys)
{
    $iLength = strlen($instructions);
    $lz = [];
    foreach ($keys as $key) {
        $count = 0;
        do {
            $dir = $instructions[$count++ % $iLength];
            $key = $maps[$key]['R' === $dir ? 1 : 0];
        } while ('Z' !== $key[2]);
        $lz[] = $count;
    }
    $lcm = array_pop($lz);

    return (int) array_reduce($lz, gmp_lcm(...), $lcm);
}

echo 'Result1: ', part1($instructions, $maps), PHP_EOL;
echo 'Result2: ', part2($instructions, $maps, $keys), PHP_EOL;

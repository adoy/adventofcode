<?php

declare(strict_types=1);

function fromSnafu(string $s): int
{
    $r = 0;
    $digits = str_split($s);
    for ($i = 0; !empty($digits); ++$i) {
        $m = match (array_pop($digits)) {
            '2' => 2,
            '1' => 1,
            '0' => 0,
            '-' => -1,
            '=' => -2,
        };
        $r += $m * (5**$i);
    }

    return $r;
}

function toSnafu(int $n): string
{
    $r = '';
    while ($n > 0) {
        $n += 2;
        $r .= match ($n % 5) {
            4 => '2',
            3 => '1',
            2 => '0',
            1 => '-',
            0 => '=',
        };
        $n = (int) ($n / 5);
    }

    return strrev($r);
}

$list = explode(PHP_EOL, trim(file_get_contents('php://stdin')));
printf("Result 1: %s\n", toSnafu(array_sum(array_map(fromSnafu(...), $list))));

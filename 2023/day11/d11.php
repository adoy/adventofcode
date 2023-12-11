<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$galaxies = [];
foreach ($lines as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        if ('#' === $char) {
            $galaxies[] = [$x, $y];
        }
    }
}
$xso = array_map(fn ($g) => $g[0], $galaxies);
$yso = array_map(fn ($g) => $g[1], $galaxies);
sort($xso);
sort($yso);

$emptyX = array_diff(range(0, strlen($lines[0]) - 1), $xso);
$emptyY = array_diff(range(0, count($lines) - 1), $yso);

foreach ([2, 1000000] as $step => $exp) {
    $result = 0;
    [$xs , $ys] = [$xso, $yso];
    while (null !== ($x1 = array_shift($xs))) {
        foreach ($xs as $x2) {
            $result += abs($x1 - $x2) + ($exp - 1) * count(array_filter($emptyX, fn ($empty) => $empty > $x1 && $empty < $x2));
        }
    }
    while (null !== ($y1 = array_shift($ys))) {
        foreach ($ys as $y2) {
            $result += abs($y1 - $y2) + ($exp - 1) * count(array_filter($emptyY, fn ($empty) => $empty > $y1 && $empty < $y2));
        }
    }

    echo 'Result ', $step + 1, ': ', $result, PHP_EOL;
}

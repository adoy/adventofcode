<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

[ , $seedsList ] = explode(':', array_shift($input));
$seedsList = array_map(intval(...), array_filter(explode(' ', $seedsList)));

$maps = [];
foreach (array_filter($input) as $line) {
    if (preg_match('/(.*):/', $line, $matches)) {
        $maps[$key = $matches[1]] = [];
    } else {
        $maps[$key][] = array_map(intval(...), explode(' ', $line));
    }
}
foreach ($maps as $name => $map) {
    $newSeedList = [];
    foreach ($seedsList as $seed) {
        $newSeed = $seed;
        foreach ($map as [ $drs, $srs, $l ]) {
            if ($seed >= $srs && $seed < $srs + $l) {
                $newSeed = $seed - $srs + $drs;
                break;
            }
        }
        $newSeedList[] = $newSeed;
    }
    $seedsList = $newSeedList;
}
echo 'Result 1: ', min($seedsList), PHP_EOL;

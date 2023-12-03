<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

$result1 = $result2 = 0;

foreach ($input as $line) {
    preg_match('/Game (\d+): (.*)/', $line, $matches);
    $gameId = $matches[1];
    $list = $matches[2];
    $sets = explode('; ', $list);
    $minRed = $minGreen = $minBlue = 0;
    $valid = true;
    foreach ($sets as $set) {
        foreach (explode(', ', $set) as $cubes) {
            [ $count, $color ] = explode(' ', $cubes);
            $count = (int) $count;
            switch ($color) {
                case 'red':
                    $valid &= ($count <= 12);
                    $minRed = max($minRed, $count);
                    break;
                case 'green':
                    $valid &= ($count <= 13);
                    $minGreen = max($minGreen, $count);
                    break;
                case 'blue':
                    $valid &= ($count <= 14);
                    $minBlue = max($minBlue, $count);
                    break;
            }
        }
    }
    if ($valid) {
        $result1 += $gameId;
    }
    $result2 += ($minRed * $minGreen * $minBlue);
}

echo "Result 1: $result1\n";
echo "Result 2: $result2\n";

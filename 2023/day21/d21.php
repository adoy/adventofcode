<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

foreach ($lines as $y => $line) {
    if (false !== ($x = strpos($line, 'S'))) {
        $lines[$y][$x] = '.';
        $start = [$y , $x ];
        break;
    }
}
$queue = new SplQueue();
$queue->enqueue([$y, $x]);
$visited[$y][$x] = true;
$result1 = [ 1, 0 ];

$width = strlen($lines[0]);
$height = count($lines);

$results = [];

for ($i = 1; $i <= 65 + 131 * 2; $i++) {
    $nextSteps = new SplQueue();
    $steps = [];
    while (!$queue->isEmpty()) {
        [$y, $x] = $queue->dequeue();
        foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$dy, $dx]) {
            $ny = $y + $dy;
            $nx = $x + $dx;
            if ($lines[(($ny%$height)+$height)%$height][(($nx%$width)+$width)%$width] === '#') {
                continue;
            }
            if (isset($visited[$ny][$nx])) {
                continue;
            }
            $visited[$ny][$nx] = true;
            $result1[$i % 2]++;
            $nextSteps->enqueue([$ny, $nx]);
        }
    }
    $results[$i] = $result1[$i % 2];
    $queue = $nextSteps;
}

$r[0] = [ 65, $results[65] ];
$r[1] = [ 65 + 131, $results[65 + 131] ];
$r[2] = [ 65 + 131 * 2, $results[65 + 131 * 2] ];

$totalSteps = 26501365;
$result2 = 0;
for ($i = 0; $i < 3; $i++) {
    $term = $r[$i][1];
    for ($j = 0; $j < 3; $j++) {
        if ($j !== $i) {
            $term = $term * ($totalSteps - $r[$j][0]) / ($r[$i][0] - $r[$j][0]);
        }
    }
    $result2 += $term;
}

echo 'Result1: ', $results[64], PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

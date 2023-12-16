<?php

declare(strict_types=1);

function x($lines, $y, $x, $dy, $dx)
{
    $width = strlen($lines[0]);
    $height = count($lines);

    $queue = new SplQueue();
    $queue->enqueue([ $y, $x, $dy, $dx ]);

    $visited = [];
    $result = 0;

    while (!$queue->isEmpty()) {
        [ $y, $x, $dy, $dx ] = $queue->dequeue();
        if ($y < 0 || $y >= $height || $x < 0 || $x >= $width || true === ($visited[$y][$x][$dy][$dx] ?? false)) {
            continue;
        }
        $result += isset($visited[$y][$x]) ? 0 : 1;
        $visited[$y][$x][$dy][$dx] = true;
        $c = $lines[$y][$x];
        if ('|' === $c && 0 === $dy) {
            $queue->enqueue([ $y - 1, $x, -1, 0 ]);
            $queue->enqueue([ $y + 1, $x, 1, 0 ]);
            continue;
        } elseif ('-' === $c && 0 === $dx) {
            $queue->enqueue([ $y, $x - 1, 0, -1 ]);
            $queue->enqueue([ $y, $x + 1, 0, 1 ]);
            continue;
        } elseif ('/' === $c) {
            [ $dy, $dx ] = [ -$dx, -$dy ];
        } elseif ('\\' === $c) {
            [ $dy, $dx ] = [ $dx, $dy ];
        }
        $queue->enqueue([ $y + $dy, $x + $dx, $dy, $dx ]);
    }

    return $result;
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$result1 = x($lines, 0, 0, 0, 1);

$result2 = 0;
for ($x = 0; $x < strlen($lines[0]); ++$x) {
    $result2 = max($result2, x($lines, 0, $x, 1, 0));
    $result2 = max($result2, x($lines, count($lines) - 1, $x, -1, 0));
}
for ($y = 0; $y < count($lines); ++$y) {
    $result2 = max($result2, x($lines, $y, 0, 0, 1));
    $result2 = max($result2, x($lines, $y, strlen($lines[0]) - 1, 0, -1));
}

echo 'Result 1: ', $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

<?php

declare(strict_types=1);

function getPriority(string $s): int
{
    return ($s > 'Z') ? ord($s) - ord('a') + 1 : ord($s) - ord('A') + 27;
}

$input = explode(PHP_EOL, trim(file_get_contents('php://stdin')));
$res1 = $res2 = 0;

foreach ($input as $v) {
    $size = strlen($v);
    $p1 = substr($v, 0, $size / 2);
    $p2 = substr($v, $size / 2);
    $found = current(array_intersect(str_split($p1), str_split($p2)));
    $res1 += getPriority($found);
}

$count = count($input);
for ($i = 0; $i < $count; $i+=3) {
    $p1 = $input[$i];
    $p2 = $input[$i+1];
    $p3 = $input[$i+2];
    $found = current(array_intersect(str_split($p1), str_split($p2), str_split($p3)));
    $res2 += getPriority($found);
}

printf("Result 1: %d\nResult 2: %d\n", $res1, $res2);

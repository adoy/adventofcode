<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode("\n", $input);

$result1 = 0;
$cardCount = array_fill(0, count($lines), 1);

foreach ($lines as $id => $line) {
    [ , $lists ] = explode(':', $line);
    [ $list1, $list2 ] = explode('|', $lists);
    $list1 = array_map(intval(...), array_filter(explode(' ', $list1)));
    $list2 = array_map(intval(...), array_filter(explode(' ', $list2)));
    $win = count(array_intersect($list1, $list2));

    $result1 += $win ? (1 << ($win-1)) : 0;
    for ($i = 1; $i <= $win; $i++) {
        $cardCount[$id+$i] += ($cardCount[$id]);
    }

}
$result2 = array_sum($cardCount);

echo "Result 1: $result1\n";
echo "Result 2: $result2\n";

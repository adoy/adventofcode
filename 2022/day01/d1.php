<?php

$data = explode(PHP_EOL, trim(file_get_contents('php://stdin')));
$count = 0;
$elves = new SplMaxHeap();
foreach ($data as $calories) {
    if ($calories) {
        $count += (int) $calories;
    } else {
        $elves->insert($count);
        $count = 0;
    }
}
$elves->insert($count);
echo 'First elf calories: ', $elves->top(), PHP_EOL;
echo 'First 3 elves calories: ', array_reduce(iterator_to_array(new LimitIterator($elves, 0, 3)), fn ($c, $i) => $c + $i), PHP_EOL;

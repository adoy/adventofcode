<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

$result1 = $result2 = 0;
foreach ($input as $line) {
    $row = array_map(intval(...), explode(' ', $line));
    do {
        $continue = false;
        $rows[] = $row;
        $c = count($row);
        $nextRow = [];
        for ($i = 1; $i < $c; ++$i) {
            $continue |= $nextRow[] = $row[$i] - $row[$i-1];
        }
        $row = $nextRow;
    } while ($continue);

    $prevVal = $nextVal = 0;
    while ($row = array_pop($rows)) {
        $nextVal += $row[count($row) - 1];
        $prevVal = $row[0] - $prevVal;
    }
    $result1 += $nextVal;
    $result2 += $prevVal;
}

echo 'Result 1: ', $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

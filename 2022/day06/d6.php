<?php

declare(strict_types=1);

$input = file_get_contents('php://stdin');
function findSequence(string $str, int $n) {
    $len = strlen($str);
    for ($i = 0; $i < $len - $n; $i++) {
        $seq = substr($str, $i, $n);
        if (strlen($seq) === $n && count(array_unique(str_split($seq))) === $n) {
            return $i + $n;
        }
    }
    return -1;
}

printf("Result 1: %d\nResult 2: %d\n", findSequence($input, 4), findSequence($input, 14));

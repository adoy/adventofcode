<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));

function H(string $input): int
{
    $length = strlen($input);
    $result = 0;
    for ($i = 0; $i < $length; ++$i) {
        $result += ord($input[$i]);
        $result *= 17;
        $result %= 256;
    }

    return $result;
}

$result1 = 0;
$result2 = 0;
$boxes = [];
foreach (explode(',', $input) as $item) {
    if (!preg_match('/(\w+)([=-])(\d+)?/', $item, $matches)) {
        exit('NO MATCH ' . $item);
    }
    @[$step, $label, $op, $value] = $matches;
    $result1 += H($step);
    switch ($op) {
        case '=':
            $boxes[H($label)][$label] = $value;
            break;
        case '-':
            unset($boxes[H($label)][$label]);
            break;
    }
}

foreach ($boxes as $box => $content) {
    $slot = 0;
    foreach ($content as $label => $value) {
        $result2+= ($box+1) * ++$slot * $value;
    }
}

echo 'Result 1: ', $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode("\n", $input);

$result1 = 0;
$gears = [];
foreach ($lines as $row => $line) {
    preg_match_all('/\d+/', $line, $matches, PREG_OFFSET_CAPTURE);
    foreach ($matches[0] as [ $number, $position ]) {
        for ($y = $row - 1; $y <= $row + 1; $y++) {
            for ($x = $position - 1; $x <= $position + strlen($number); $x+=($y === $row ? strlen($number)+1 : 1)) {
                switch ($lines[$y][$x] ?? '.') {
                    case '.':
                        break;
                    case '*':
                        $gears[$x . ',' . $y][] = $number;
                    default:
                        $result1 += $number;
                        break 3;
                }
            }
        }
    }
}
$result2 = array_sum(array_map(array_product(...), array_filter($gears, fn($e) => count($e) > 1)));

echo "Result 1: $result1\n";
echo "Result 2: $result2\n";

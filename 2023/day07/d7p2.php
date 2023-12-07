<?php

declare(strict_types=1);

function handsVal($a) {
    $val = array_count_values(str_split($a));
    if (false !== ($pos = strpos($a, 'J'))) {
        $maxVal = 0;
        foreach (['2','3','4','5','6','7','8','9','T','Q','K','A'] as $c) {
            $newHand = $a;
            $newHand[$pos] = $c;
            $maxVal = max($maxVal, handsVal($newHand));
        }
        return $maxVal;
    }
    if (count($val) === 1) {
        return 7;
    } elseif (count($val) === 2 && in_array(4, $val)) {
        return 6;
    } elseif (count($val) == 2 && in_array(3, $val) && in_array(2, $val)) {
        return 5;
    } elseif (in_array(3, $val)) {
        return 4;
    } elseif (count($val) === 3 && in_array(2, $val)) {
        return 3;
    } elseif (in_array(2, $val)){
        return 2;
    }
    return 1;
}

function cardVal($a) {
    return array_search($a, ['J', '2','3','4','5','6','7','8','9','T','Q','K','A']);
}

function compare($a, $b) {
    return handsVal($a[0]) <=> handsVal($b[0]) ?:
        cardVal($a[0][0]) <=> cardVal($b[0][0]) ?:
        cardVal($a[0][1]) <=> cardVal($b[0][1]) ?:
        cardVal($a[0][2]) <=> cardVal($b[0][2]) ?:
        cardVal($a[0][3]) <=> cardVal($b[0][3]) ?:
        cardVal($a[0][4]) <=> cardVal($b[0][4]);
}

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

$hands = [];
foreach ($input as $line) {
    $hand = explode(' ', $line);
    $hands[] = $hand;
}

usort($hands, compare(...));
$result = 0;
foreach ($hands as $i => $hand) {
    $result += ($i + 1) * $hand[1];
}

echo 'Result 2: ', $result, PHP_EOL;

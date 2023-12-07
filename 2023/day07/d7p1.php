<?php

declare(strict_types=1);

class Hand
{
    private int $value;

    public function __construct(
        private readonly string $hand,
    ) {
        $this->value = $this->getValue($hand);
    }

    private function getValue(string $a): int
    {
        $val = array_count_values(str_split($a));
        if (1 === count($val)) {
            return 7;
        } elseif (2 === count($val) && in_array(4, $val)) {
            return 6;
        } elseif (2 == count($val) && in_array(3, $val) && in_array(2, $val)) {
            return 5;
        } elseif (in_array(3, $val)) {
            return 4;
        } elseif (3 === count($val) && in_array(2, $val)) {
            return 3;
        } elseif (in_array(2, $val)) {
            return 2;
        }

        return 1;
    }

    public function compare(Hand $other): int
    {
        return $this->value <=> $other->value ?:
            $this->getCardValue(0) <=> $other->getCardValue(0) ?:
            $this->getCardValue(1) <=> $other->getCardValue(1) ?:
            $this->getCardValue(2) <=> $other->getCardValue(2) ?:
            $this->getCardValue(3) <=> $other->getCardValue(3) ?:
            $this->getCardValue(4) <=> $other->getCardValue(4);
    }

    private function getCardValue(int $index): int
    {
        return array_search($this->hand[$index], ['2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K', 'A']);
    }
}

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

$hands = [];
foreach ($input as $line) {
    $hand = explode(' ', $line);
    $hands[] = [ new Hand($hand[0]), $hand[1] ];
}

usort($hands, fn ($a, $b) => $a[0]->compare($b[0]));
$result = 0;
foreach ($hands as $i => $hand) {
    $result += ($i + 1) * $hand[1];
}

echo 'Result 1: ', $result, PHP_EOL;

<?php

declare(strict_types=1);

class Packet
{
    private readonly array $value;

    public function __construct(string $input)
    {
        $this->value = json_decode($input, true);
    }

    private static function compareValue(int|array $left, int|array $right): int
    {
        if (is_int($left) && is_int($right)) {
            return $left <=> $right;
        }
        $left = (array) $left;
        $right = (array) $right;

        $leftCount = count($left);
        $rightCount = count($right);

        $c = min($leftCount, $rightCount);
        for ($i = 0; $i < $c; ++$i) {
            if ($res = self::compareValue($left[$i], $right[$i])) {
                return $res;
            }
        }

        return $leftCount <=> $rightCount;
    }

    public function compare(Packet $other): int
    {
        return self::compareValue($this->value, $other->value);
    }
}

function sumOfIndicesInTheRightOrder(array $packets): int
{
    $result = 0;
    for ($i = 1; count($packets); ++$i) {
        if (-1 == array_shift($packets)->compare(array_shift($packets))) {
            $result += $i;
        }
    }

    return $result;
}

function decoderKey(array $packets): int
{
    $divider1 = new Packet('[[2]]');
    $divider2 = new Packet('[[6]]');
    $packets = [ ...$packets, $divider1, $divider2 ];
    usort($packets, fn ($p1, $p2) => $p1->compare($p2));

    return (array_search($divider1, $packets) + 1) * (array_search($divider2, $packets) + 1);
}

$input = explode(PHP_EOL, trim(file_get_contents('php://stdin')));
$packets = array_map(fn ($s) => new Packet($s), array_filter($input, fn (string $s): bool => '' !== $s));

printf("Result 1: %d\nResult 2: %d\n", sumOfIndicesInTheRightOrder($packets), decoderKey($packets));

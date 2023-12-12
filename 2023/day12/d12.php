<?php

declare(strict_types=1);

class S {
    private int $length;
    private array $cache = [];

    public function __construct(
        private readonly string $s,
        private readonly array $def
    ) {
        $this->length = strlen($s);
    }

    private function solve(int $pos, int $blockPos, int $blockSize): int
    {
        if ($pos === $this->length) {
            return (
                (0 === $blockSize && count($this->def) === $blockPos) ||
                (count($this->def) - 1 === $blockPos && $blockSize === $this->def[$blockPos])
            ) ? 1 : 0;
        }

        if (null === ($this->cache[$pos][$blockPos][$blockSize] ?? null)) {
            $result = 0;
            switch ($c = $this->s[$pos]) {
                case '?':
                case '#':
                    $result += $this->solve($pos + 1, $blockPos, $blockSize + 1);
                    if ($c !== '?') {
                        break;
                    }
                case '.':
                    if (0 === $blockSize) {
                        $result += $this->solve($pos + 1, $blockPos, 0);
                    } elseif ($blockPos < count($this->def) && $blockSize === $this->def[$blockPos]) {
                        $result += $this->solve($pos + 1, $blockPos + 1, 0);
                    }
            }

            $this->cache[$pos][$blockPos][$blockSize] = $result;
        }

        return $this->cache[$pos][$blockPos][$blockSize];
    }

    public function __invoke(): int {
        return $this->solve(0, 0, 0);
    }
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$result1 = $result2 = 0;

foreach ($lines as $line) {
    [ $arrangement, $def ] = explode(' ', $line);

    $result1 += (new S($arrangement, array_map(intval(...), explode(',', $def))))();

    $arrangement = implode('?', array_fill(0, 5, $arrangement));
    $def = implode(',', array_fill(0, 5, $def));

    $result2 += (new S($arrangement, array_map(intval(...), explode(',', $def))))();
}

echo 'Result 1: ', $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

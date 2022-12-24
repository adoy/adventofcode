<?php

declare(strict_types=1);

class Grid
{
    private $elves = [];
    private $map = [];
    private $preferedDirection = 0;

    public function __construct(string $input)
    {
        foreach (explode(PHP_EOL, $input) as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                if ('#' === $char) {
                    $this->map[$y][$x] = $this->elves[] = new Elf($x, $y);
                }
            }
        }
    }

    public function isEmpty(int $x, int $y): bool
    {
        return empty($this->map[$y][$x] ?? []);
    }

    public function round(): bool
    {
        $moved = false;
        $newMap = [];
        $proposedMap = [];
        foreach ($this->elves as $elf) {
            if ($proposed = $elf->proposeMove($this, $this->preferedDirection)) {
                $proposedMap[$proposed[1]][$proposed[0]][] = $elf;
            } else {
                $newMap[$elf->y][$elf->x] = $elf;
            }
        }

        foreach ($proposedMap as $y => $row) {
            foreach ($row as $x => $elves) {
                if (1 === count($elves)) {
                    $elves[0]->move($x, $y);
                    $moved = true;
                }
                foreach ($elves as $elf) {
                    $newMap[$elf->y][$elf->x] = $elf;
                }
            }
        }
        $this->map = $newMap;
        $this->preferedDirection = ($this->preferedDirection + 1) % 4;

        return $moved;
    }

    public function emptyGroundTiles(): int
    {
        $minX = min(array_map(fn (Elf $elf) => $elf->x, $this->elves));
        $maxX = max(array_map(fn (Elf $elf) => $elf->x, $this->elves));
        $minY = min(array_map(fn (Elf $elf) => $elf->y, $this->elves));
        $maxY = max(array_map(fn (Elf $elf) => $elf->y, $this->elves));

        return ($maxX - $minX + 1) * ($maxY - $minY + 1) - count($this->elves);
    }
}

class Elf
{
    private const N = [ 0, -1 ];
    private const S = [ 0,  1 ];
    private const W = [-1,  0 ];
    private const E = [ 1,  0 ];
    private const NE = [ 1, -1 ];
    private const NW = [-1, -1 ];
    private const SE = [ 1,  1 ];
    private const SW = [-1,  1 ];

    private const PROPOSED_DIRECTIONS = [
        [ 'direction' => self::N, 'check' => [ self::N, self::NE, self::NW ] ],
        [ 'direction' => self::S, 'check' => [ self::S, self::SE, self::SW ] ],
        [ 'direction' => self::W, 'check' => [ self::W, self::NW, self::SW ] ],
        [ 'direction' => self::E, 'check' => [ self::E, self::NE, self::SE ] ],
    ];

    public function __construct(
        public int $x,
        public int $y,
    ) {
    }

    public function proposeMove(Grid $grid, int $preferedDirection): ?array
    {
        if ($this->needToMove($grid)) {
            for ($i = 0; $i < 4; ++$i) {
                $direction = ($preferedDirection + $i) % 4;
                if ($this->canMove($grid, $direction)) {
                    return [
                        $this->x + self::PROPOSED_DIRECTIONS[$direction]['direction'][0],
                        $this->y + self::PROPOSED_DIRECTIONS[$direction]['direction'][1]
                    ];
                }
            }
        }

        return null;
    }

    public function needToMove(Grid $grid): bool
    {
        for ($y = -1; $y <= 1; ++$y) {
            for ($x = -1; $x <= 1; ++$x) {
                if (0 === $x && 0 === $y) {
                    continue;
                }
                if (!$grid->isEmpty($this->x + $x, $this->y + $y)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canMove(Grid $grid, int $direction): bool
    {
        foreach (self::PROPOSED_DIRECTIONS[$direction]['check'] as $check) {
            if (!$grid->isEmpty($this->x + $check[0], $this->y + $check[1])) {
                return false;
            }
        }

        return true;
    }

    public function move(int $x, int $y): void
    {
        [ $this->x, $this->y ] = [ $x, $y ];
    }
}

$input = trim(file_get_contents('php://stdin'));
$grid = new Grid($input);
for ($i = 1; $i <= 10; ++$i) {
    $continue = $grid->round();
}
printf("Result 1: %d\n", $grid->emptyGroundTiles());
while ($grid->round()) {
    ++$i;
}
printf("Result 2: %d\n", $i);

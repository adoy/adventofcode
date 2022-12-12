<?php

declare(strict_types=1);

class Square
{
    public bool $isVisited = false;

    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $elevation
    ) {
    }

    public function isAccessibleFrom(Square $other): bool
    {
        return ($this->elevation - $other->elevation) <= 1;
    }
}

class Grid
{
    private array $grid = [];
    public readonly Square $startPos;
    public readonly Square $endPos;

    public function __construct(string $input)
    {
        foreach (explode(PHP_EOL, trim($input)) as $y => $line) {
            foreach (str_split($line) as $x => $value) {
                $this->grid[$y][$x] = match ($value) {
                    'S' => $this->startPos = new Square($x, $y, 0),
                    'E' => $this->endPos = new Square($x, $y, 25),
                    default => new Square($x, $y, ord($value) - ord('a')),
                };
            }
        }
    }

    private function at(int $x, int $y): ?Square
    {
        return $this->grid[$y][$x] ?? null;
    }

    public function accessibleUnvisitedSquares(Square $square): array
    {
        return array_filter([
            $this->at($square->x - 1, $square->y),
            $this->at($square->x + 1, $square->y),
            $this->at($square->x, $square->y - 1),
            $this->at($square->x, $square->y + 1),
        ], fn ($v) => $v && !$v->isVisited && $square->isAccessibleFrom($v));
    }
}

$input = trim(file_get_contents('php://stdin'));
$grid = new Grid($input);
$step = $res1 = $res2 = 0;
$visitedSquares = [ $grid->endPos ];

while (++$step) {
    $nextVisitedSquares = [];

    foreach ($visitedSquares as $current) {
        foreach ($grid->accessibleUnvisitedSquares($current) as $square) {
            if (!$res2 && 0 == $square->elevation) {
                $res2 = $step;
            }
            if ($square === $grid->startPos) {
                $res1 = $step;
                break 3;
            }
            $square->isVisited = true;
            $nextVisitedSquares[] = $square;
        }
    }
    $visitedSquares = $nextVisitedSquares;
}

printf("Result 1: %d\nResult 2: %d\n", $res1, $res2);

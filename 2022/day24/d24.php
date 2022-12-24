<?php

declare(strict_types=1);

function positiveModulo(int $a, int $b): int
{
    return ($a % $b + $b) % $b;
}

class Blizard
{
    public function __construct(
        public string $direction,
        public int $x,
        public int $y
    ) {
    }

    public function move(Grid $grid): void
    {
        switch ($this->direction) {
            case '>':
                $this->x = positiveModulo($this->x, $grid->width - 2) + 1;
                break;
            case '<':
                $this->x = positiveModulo($this->x - 2, $grid->width - 2) + 1;
                break;
            case 'v':
                $this->y = positiveModulo($this->y, $grid->height - 2) + 1;
                break;
            case '^':
                $this->y = positiveModulo($this->y - 2, $grid->height - 2) + 1;
                break;
        }
    }
}

class Grid
{
    private const MOVES = [
        [ 0, -1 ],
        [ 1, 0 ],
        [ 0, 1 ],
        [ -1, 0 ],
        [ 0, 0 ],
    ];

    private $blizards = [];
    private array $grid= [];
    public readonly int $width;
    public readonly int $height;
    public readonly array $startPos;
    public readonly array $endPos;

    public function __construct($input)
    {
        $lines = explode(PHP_EOL, $input);
        foreach ($lines as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                switch ($char) {
                    case '>':
                    case '<':
                    case 'v':
                    case '^':
                        $this->blizards[] = new Blizard($char, $x, $y);
                        break;
                }
            }
        }

        $this->height = count($lines);
        $this->width = strlen($lines[0]);
        $this->startPos = [ strpos($lines[0], '.'), 0 ];
        $this->endPos = [ strpos($lines[$this->height - 1], '.'), $this->height - 1 ];
    }

    public function isInbound(int $x, int $y): bool
    {
        return ($x > 0 && $x < $this->width - 1 && $y > 0 && $y < $this->height - 1)
            || [ $x, $y ] === $this->endPos
            || [ $x, $y ] === $this->startPos;
    }

    public function isInBlizard(int $x, int $y): bool
    {
        return $this->grid[$y][$x] ?? false;
    }

    public function move(): void
    {
        $this->grid = [];
        foreach ($this->blizards as $blizard) {
            $blizard->move($this);
            $this->grid[$blizard->y][$blizard->x] = true;
        }
    }

    public function searchShortestPath(array $startPos = null, array $endPos = null): int
    {
        $time = 0;
        $queue = [ $startPos ?? $this->startPos ];
        $endPos ??= $this->endPos;

        do {
            if (in_array($endPos, $queue)) {
                return $time;
            }
            $this->move();
            $newQueue = [];

            foreach ($queue as $pos) {
                foreach (self::MOVES as $move) {
                    $newPos = [ $pos[0] + $move[0], $pos[1] + $move[1] ];
                    if ($this->isInbound(...$newPos) && !$this->isInBlizard(...$newPos)) {
                        $newQueue[$newPos[0] . '|' . $newPos[1]] = $newPos;
                    }
                }
            }

            $queue = $newQueue;
        } while (count($queue) && ++$time);

        return -1;
    }
}

$input = trim(file_get_contents('php://stdin'));
$grid = new Grid($input);
$res1 = $res2 = $grid->searchShortestPath();
$res2 += $grid->searchShortestPath($grid->endPos, $grid->startPos);
$res2 += $grid->searchShortestPath();

printf("Result 1: %d\nResult 2: %d\n", $res1, $res2);

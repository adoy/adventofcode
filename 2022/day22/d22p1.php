<?php

declare(strict_types=1);

class Grid
{
    private const DIRECTIONS = [
        [ 1, 0 ],
        [ 0, 1 ],
        [ -1, 0 ],
        [ 0, -1 ],
    ];

    public array $grid = [];
    public array $position;
    public int $direction = 0;
    private int $width = 0;
    private int $height = 0;

    public function __construct(string $input)
    {
        foreach (explode(PHP_EOL, $input) as $line) {
            $this->grid[] = str_split($line);
            $this->width = max($this->width, strlen($line));
        }
        $this->height = count($this->grid);

        $this->position = $this->getStartingPos();
    }

    public function getStartingPos(): array
    {
        foreach ($this->grid[0] as $x => $value) {
            if ('.' === $value) {
                return [$x, 0];
            }
        }
    }

    public function left(): void
    {
        $this->direction = ($this->direction + 3) % 4;
    }

    public function right(): void
    {
        $this->direction = ($this->direction + 1) % 4;
    }

    public function move(int $steps): void
    {
        for ($i = 0; $i < $steps; ++$i) {
            [ $x, $y ] = $this->position;
            do {
                $x += self::DIRECTIONS[$this->direction][0];
                $y += self::DIRECTIONS[$this->direction][1];

                if ($x < 0) {
                    $x = $this->width - 1;
                }
                if ($x >= $this->width) {
                    $x = 0;
                }
                if ($y < 0) {
                    $y = $this->height - 1;
                }
                if ($y >= $this->height) {
                    $y = 0;
                }
            } while (($this->grid[$y][$x] ?? ' ') === ' ');
            if (($this->grid[$y][$x] ?? ' ') === '#') {
                break;
            }
            $this->position = [$x, $y];
        }
    }
}

$input = file_get_contents('php://stdin');
[ $map, $instructions ] = explode(PHP_EOL . PHP_EOL, $input);

$map = new Grid($map);
$instructionPos = 0;
while (preg_match('/(\d+|L|R)/', $instructions, $matches, PREG_OFFSET_CAPTURE, $instructionPos)) {
    $instructionPos += strlen($matches[0][0]);
    if ('L' === $matches[1][0]) {
        $map->left();
    } elseif ('R' === $matches[1][0]) {
        $map->right();
    } else {
        $map->move((int) $matches[1][0]);
    }
}
printf("Result 1: %d\n", 1000 * ($map->position[1] + 1) + ($map->position[0] + 1) * 4 + $map->direction);

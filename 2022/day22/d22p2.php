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

    private const FRONT = 0;
    private const RIGHT = 1;
    private const BACK  = 2;
    private const LEFT  = 3;

    public array $grid = [];
    public array $position;
    public int $direction = 0;
    private int $width = 0;
    private int $height = 0;

    public function __construct(string $input, private int $squareSize = 50)
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

    public function assembleCube(): void
    {
        [ $x, $y ] = $this->getStartingPos();
        [ $startX, $startY ] = [ $x, --$y ];
        $direction = 0;
        do {
            [ $nextX, $nextY ] = [ $x + self::DIRECTIONS[$direction][0], $y + self::DIRECTIONS[$direction][1] ];
            if (($this->grid[$nextY][$nextX] ?? ' ') === ' ') {
                $touchingX = $nextX + self::DIRECTIONS[($direction + self::RIGHT)%4][0];
                $touchingY = $nextY + self::DIRECTIONS[($direction + self::RIGHT)%4][1];
                if (($this->grid[$touchingY][$touchingX] ?? ' ') !== ' ') {
                    $borders[] = [ $x = $nextX, $y = $nextY, ($direction + self::LEFT)%4 ];
                } else {
                    $direction = ($direction + self::RIGHT)%4;
                    $x = $nextX;
                    $y = $nextY;
                }
            } else {
                $this->grid[$y][$x] = [
                    ($direction + self::BACK) % 4 => [
                        $x + self::DIRECTIONS[($direction + 1)%4][0],
                        $y + self::DIRECTIONS[($direction + 1)%4][1],
                        ($direction + self::RIGHT) % 4,
                    ],
                    ($direction + self::LEFT) % 4 => [
                        $nextX,
                        $nextY,
                        $direction,
                    ],
                ];
                $direction = ($direction + self::LEFT) % 4;
            }
        } while ([ $startX, $startY ] !== [ $x, $y ]);

        $corners = [];
        foreach ($borders as $index => [ $x, $y, $direction ]) {
            if (is_array($this->grid[$y][$x] ?? null)) {
                $corners[] = $index;
            }
        }

        $tomap = count($borders) - count($corners);
        $mapped = 0;
        for ($i = 0; $tomap !== $mapped; $i+=$this->squareSize) {
            foreach ($corners as $corner) {
                for ($j = $this->squareSize - 1; $j >= 0; --$j) {
                    [ $xa, $ya, $da ] = $borders[($corner+$i+$j)%count($borders)];
                    [ $xb, $yb, $db ] = $borders[($corner-$i-$j+count($borders))%count($borders)];

                    if (!is_array($this->grid[$ya][$xa] ?? null) && !is_array($this->grid[$yb][$xb] ?? null)) {
                        $this->grid[$ya][$xa] = [
                            $da => [
                                $xb + self::DIRECTIONS[($db + self::BACK)%4][0],
                                $yb + self::DIRECTIONS[($db + self::BACK)%4][1],
                                ($db + self::BACK)%4,
                            ],
                        ];
                        $this->grid[$yb][$xb] = [
                            $db => [
                                $xa + self::DIRECTIONS[($da + self::BACK)%4][0],
                                $ya + self::DIRECTIONS[($da + self::BACK)%4][1],
                                ($da + self::BACK)%4,
                            ],
                        ];
                        $mapped+=2;
                    }
                }
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
            $x += self::DIRECTIONS[$this->direction][0];
            $y += self::DIRECTIONS[$this->direction][1];
            $direction = $this->direction;

            if (is_array($this->grid[$y][$x] ?? null)) {
                [ $x, $y, $direction ] = $this->grid[$y][$x][$direction];
            }

            if (($this->grid[$y][$x] ?? ' ') === '#') {
                break;
            }
            $this->position = [$x, $y];
            $this->direction = $direction;
        }
    }
}

$input = file_get_contents('php://stdin');
[ $map, $instructions ] = explode(PHP_EOL . PHP_EOL, $input);

$map = new Grid($map, (int) $_SERVER['argv'][1] ?? 50);
$map->assembleCube();
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
printf("Result 2: %d\n", 1000 * ($map->position[1] + 1) + ($map->position[0] + 1) * 4 + $map->direction);

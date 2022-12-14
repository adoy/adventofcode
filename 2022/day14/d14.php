<?php

declare(strict_types=1);

class Grid
{
    private const SOURCE = [ 500, 0 ];
    private array $grid = [];
    private int $maxY = 0;

    public function __construct(string $input)
    {
        $input = explode(PHP_EOL, $input);
        foreach ($input as $line) {
            $this->addRockPath($line);
        }
    }

    private function addRockPath(string $input): void
    {
        $coords = array_map(fn ($x) => array_map(fn ($d) => (int) $d, explode(',', $x)), explode(' -> ', $input));
        [ $posX, $posY ] = array_pop($coords);

        while ([ $nextX, $nextY ] = array_pop($coords)) {
            if ($diffY = $nextY <=> $posY) {
                for ($y = $posY; $y != $nextY; $y += $diffY) {
                    $this->setRock($posX, $y);
                }
            } elseif ($diffX = $nextX <=> $posX) {
                for ($x = $posX; $x != $nextX; $x += $diffX) {
                    $this->setRock($x, $posY);
                }
            }
            $this->setRock($nextX, $nextY);
            [ $posX, $posY ] = [ $nextX, $nextY ];
        }
    }

    private function setRock(int $x, int $y): void
    {
        $this->maxY = max($this->maxY, $y);
        $this->grid[$y][$x] = true;
    }

    private function isFree(int $x, int $y): bool
    {
        if ($y >= $this->maxY+2) {
            return false;
        }

        return !isset($this->grid[$y][$x]);
    }

    public function dropSandUntilFlowing(): int
    {
        $count = 0;
        while ($this->dropSand($this->maxY)) {
            ++$count;
        }

        return $count;
    }

    public function dropSandUntilSourceBlocked(): int
    {
        $count = 0;
        do {
            $sand = $this->dropSand($this->maxY + 2);
            ++$count;
        } while ($sand != self::SOURCE);

        return $count;
    }

    private function dropSand(int $maxY): ?array
    {
        [ $x, $y ] = self::SOURCE;
        do {
            while ($this->isFree($x, $y+1) && $y < $maxY) {
                ++$y;
            }
            if ($this->isFree($x-1, $y+1)) {
                --$x;
                ++$y;
                continue;
            } elseif ($this->isFree($x+1, $y+1)) {
                ++$x;
                ++$y;
                continue;
            }
            $this->grid[$y][$x] = true;

            return [ $x, $y ];
        } while ($y < $maxY);

        return null;
    }
}

$input = trim(file_get_contents('php://stdin'));

$grid1 = new Grid($input);
$grid2 = clone $grid1;
printf("Result1: %d\nResult2: %d\n", $grid1->dropSandUntilFlowing(), $grid2->dropSandUntilSourceBlocked());

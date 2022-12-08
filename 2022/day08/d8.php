<?php

declare(strict_types=1);

readonly class Tree
{
    public function __construct(
        public int $x,
        public int $y,
        public int $height
    ) {
    }

    public function isHigherThan(Tree $other): bool
    {
        return $this->height > $other->height;
    }

    public function isHigherThanAll(iterable $trees): bool
    {
        foreach ($trees as $tree) {
            if (!$this->isHigherThan($tree)) {
                return false;
            }
        }

        return true;
    }

    public function getViewingDistance(iterable $trees): int
    {
        $res = 0;
        foreach ($trees as $tree) {
            ++$res;
            if (!$this->isHigherThan($tree)) {
                break;
            }
        }

        return $res;
    }
}

class Grid
{
    /** @var array<int, array<int, Tree>> */
    private array $map = [];

    private readonly int $width;
    private readonly int $height;

    public function __construct(string $input)
    {
        $input = explode(PHP_EOL, $input);
        $this->height = count($input);
        $this->width = strlen($input[0]);
        for ($y = 0; $y < $this->height; ++$y) {
            for ($x = 0; $x < $this->width; ++$x) {
                $this->map[$y][$x] = new Tree($x, $y, (int) $input[$y][$x]);
            }
        }
    }

    public function visibleTreeCount(): int
    {
        $count = 0;
        foreach ($this->allInnerTrees() as $tree) {
            $count += $this->isVisible($tree) ? 1 : 0;
        }

        return $count + $this->height * 2 + $this->width * 2 - 4;
    }

    public function highestScenicScore(): int
    {
        $max = 0;
        foreach ($this->allInnerTrees() as $tree) {
            $max = max($max, $this->getTreeViewingDistance($tree));
        }

        return $max;
    }

    private function allInnerTrees(): iterable
    {
        for ($y = 1; $y < $this->height - 1; ++$y) {
            for ($x = 1; $x < $this->width - 1; ++$x) {
                yield $this->map[$y][$x];
            }
        }
    }

    private function topTreesFrom(Tree $tree): iterable
    {
        for ($y = $tree->y - 1; $y >= 0; --$y) {
            yield $this->map[$y][$tree->x];
        }
    }

    private function rightTreesFrom(Tree $tree): iterable
    {
        for ($x = $tree->x + 1; $x < $this->width; ++$x) {
            yield $this->map[$tree->y][$x];
        }
    }

    private function bottomTreesFrom(Tree $tree): iterable
    {
        for ($y = $tree->y + 1; $y < $this->height; ++$y) {
            yield $this->map[$y][$tree->x];
        }
    }

    private function leftTreesFrom(Tree $tree): iterable
    {
        for ($x = $tree->x - 1; $x >= 0; --$x) {
            yield $this->map[$tree->y][$x];
        }
    }

    private function isVisible(Tree $tree): bool
    {
        return $tree->isHigherThanAll($this->topTreesFrom($tree))
            || $tree->isHigherThanAll($this->rightTreesFrom($tree))
            || $tree->isHigherThanAll($this->bottomTreesFrom($tree))
            || $tree->isHigherThanAll($this->leftTreesFrom($tree));
    }

    private function getTreeViewingDistance(Tree $tree): int
    {
        return $tree->getViewingDistance($this->topTreesFrom($tree))
            * $tree->getViewingDistance($this->rightTreesFrom($tree))
            * $tree->getViewingDistance($this->bottomTreesFrom($tree))
            * $tree->getViewingDistance($this->leftTreesFrom($tree));
    }
}

$input = trim(file_get_contents('php://stdin'));
$grid = new Grid($input);

printf("Result 1: %d\nResult 2: %d\n", $grid->visibleTreeCount(), $grid->highestScenicScore());

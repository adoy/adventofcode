<?php

declare(strict_types=1);

class Cube
{
    public const FACES = 6;

    private const UP    = 1 << 0;
    private const DOWN  = 1 << 1;
    private const LEFT  = 1 << 2;
    private const RIGHT = 1 << 3;
    private const FRONT = 1 << 4;
    private const BACK  = 1 << 5;

    private int $touched = 0;

    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $z,
    ) {
    }

    public function isTouching(Cube $cube): bool
    {
        return
            $this->x === $cube->x && $this->y === $cube->y && 1 === abs($this->z - $cube->z) ||
            $this->x === $cube->x && $this->z === $cube->z && 1 === abs($this->y - $cube->y) ||
            $this->y === $cube->y && $this->z === $cube->z && 1 === abs($this->x - $cube->x)
        ;
    }

    public function tryTouching(Cube $cube): void
    {
        if ($this->x === $cube->x && $this->y === $cube->y && 1 === abs($this->z - $cube->z)) {
            $this->touched |= $this->z > $cube->z ? self::BACK : self::FRONT;
        }
        if ($this->x === $cube->x && $this->z === $cube->z && 1 === abs($this->y - $cube->y)) {
            $this->touched |= $this->y > $cube->y ? self::DOWN : self::UP;
        }
        if ($this->y === $cube->y && $this->z === $cube->z && 1 === abs($this->x - $cube->x)) {
            $this->touched |= $this->x > $cube->x ? self::LEFT : self::RIGHT;
        }
    }

    public function countTouchedSides(): int
    {
        return substr_count(decbin($this->touched), '1');
    }
}

class Area
{
    private array $area;
    private readonly int $minX;
    private readonly int $maxX;
    private readonly int $minY;
    private readonly int $maxY;
    private readonly int $minZ;
    private readonly int $maxZ;

    public function __construct(
        array $cubes
    ) {
        foreach ($cubes as $cube) {
            $this->area[$cube->x][$cube->y][$cube->z] = $cube;
        }
        $this->minX = min(array_map(fn ($c) => $c->x, $cubes)) - 1;
        $this->maxX = max(array_map(fn ($c) => $c->x, $cubes)) + 1;
        $this->minY = min(array_map(fn ($c) => $c->y, $cubes)) - 1;
        $this->maxY = max(array_map(fn ($c) => $c->y, $cubes)) + 1;
        $this->minZ = min(array_map(fn ($c) => $c->z, $cubes)) - 1;
        $this->maxZ = max(array_map(fn ($c) => $c->z, $cubes)) + 1;
    }

    public function getCornerCube(): Cube
    {
        return $this->getCube($this->minX, $this->minY, $this->minZ);
    }

    public function getCube(int $x, int $y, int $z): Cube
    {
        return $this->area[$x][$y][$z] ??= new Cube($x, $y, $z);
    }

    public function getUnvisitedCubesArround(Cube $cube): array
    {
        return array_filter([
            ($cube->z - 1 >= $this->minZ && !isset($this->area[$cube->x][$cube->y][$cube->z - 1])) ? $this->getCube($cube->x, $cube->y, $cube->z - 1) : null,
            ($cube->z + 1 <= $this->maxZ && !isset($this->area[$cube->x][$cube->y][$cube->z + 1])) ? $this->getCube($cube->x, $cube->y, $cube->z + 1) : null,
            ($cube->y - 1 >= $this->minY && !isset($this->area[$cube->x][$cube->y - 1][$cube->z])) ? $this->getCube($cube->x, $cube->y - 1, $cube->z) : null,
            ($cube->y + 1 <= $this->maxY && !isset($this->area[$cube->x][$cube->y + 1][$cube->z])) ? $this->getCube($cube->x, $cube->y + 1, $cube->z) : null,
            ($cube->x - 1 >= $this->minX && !isset($this->area[$cube->x - 1][$cube->y][$cube->z])) ? $this->getCube($cube->x - 1, $cube->y, $cube->z) : null,
            ($cube->x + 1 <= $this->maxX && !isset($this->area[$cube->x + 1][$cube->y][$cube->z])) ? $this->getCube($cube->x + 1, $cube->y, $cube->z) : null,
        ]);
    }
}

function loadCubes(string $input): array
{
    $cubes = [];
    foreach (explode(PHP_EOL, $input) as $line) {
        [ $x, $y, $z ] = explode(',', $line);
        $cubes[] = new Cube((int) $x, (int) $y, (int) $z);
    }

    return $cubes;
}

function getLavaDropletSurfaceArea(string $input): int
{
    $cubes = loadCubes($input);
    $res1 = 0;
    while ($c1 = array_shift($cubes)) {
        foreach ($cubes as $c2) {
            $c1->tryTouching($c2);
        }
        $res1 += 6 - 2 * $c1->countTouchedSides();
    }

    return $res1;
}

function getLavaDropletExteriorSurfaceArea(string $input): int
{
    $cubes = loadCubes($input);
    $area = new Area($cubes);
    $queue = [ $area->getCornerCube() ];
    while ($cube = array_pop($queue)) {
        foreach ($cubes as $c) {
            $c->tryTouching($cube);
        }
        $queue = array_merge($queue, $area->getUnvisitedCubesArround($cube));
    }

    $count = 0;
    foreach ($cubes as $c) {
        $count += $c->countTouchedSides();
    }

    return $count;
}

$input = trim(file_get_contents('php://stdin'));
printf("Result 1: %d\nResult 2: %d\n", getLavaDropletSurfaceArea($input), getLavaDropletExteriorSurfaceArea($input));

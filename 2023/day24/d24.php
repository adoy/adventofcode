<?php

declare(strict_types=1);

// https://fr.wikipedia.org/wiki/Intersection_(g%C3%A9om%C3%A9trie)#Deux_droites
// Point d'intersection de deux droites ( a1x + b1y = c1, a2x + b2y = c2 ) dans un plan
//
// xs = (c1b2 - b1c2) / (a1b2 - b1a2)
// ys = (a1c2 - c1a2) / (a1b2 - b1a2)
//
// Si (a1b2 - b1a2) == 0, les droites sont parallèles

// Un point (px, py) est sur la droite si
// px = x + vx * t
// py = y + vy * t
//
// t = (px - x) / vx
// t = (py - y) / vy
//
// Donc
// (px - x) / vx = (py - y) / vy
// vy * (px - x) = vx * (py - y)
// vy * px - vy * x = vx * py - vx * y
// vy * px - vx * py = vy * x - vx * y
//
// Donc
// a = vy
// b = -vx
// c = vy * x - vx * y

class Hailstone
{
    public readonly int $a;
    public readonly int $b;
    public readonly int $c;

    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $z,
        public readonly int $vx,
        public readonly int $vy,
        public readonly int $vz
    ) {
        $this->a = $vy;
        $this->b = -$vx;
        $this->c = $x * $vy - $y * $vx;
    }

    public function __toString(): string
    {
        return sprintf('pos=<x=%d, y=%d, z=%d>, vel=<x=%d, y=%d, z=%d>', $this->x, $this->y, $this->z, $this->vx, $this->vy, $this->vz);
    }
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$hailstones = [];
foreach ($lines as $line) {
    if (!preg_match('/^(\d+),\s+(\d+),\s+(\d+)\s+@\s+(-?\d+),\s+(-?\d+),\s+(-?\d+)$/', $line, $matches)) {
        throw new \Exception('Invalid line: ' . $line);
    }
    $hailstones[] = $h = new Hailstone(...array_map(intval(...), array_slice($matches, 1)));
}

$result1 = 0;
for ($i = 0; $i < count($hailstones); ++$i) {
    for ($j = $i+1; $j < count($hailstones); ++$j) {
        $h1 = $hailstones[$i];
        $h2 = $hailstones[$j];

        if (0 === $h1->a * $h2->b - $h1->b * $h2->a) {
            continue;
        }
        $xs = ($h1->c * $h2->b - $h1->b * $h2->c) / ($h1->a * $h2->b - $h1->b * $h2->a);
        $ys = ($h1->a * $h2->c - $h1->c * $h2->a) / ($h1->a * $h2->b - $h1->b * $h2->a);
        $t1 = ($xs - $h1->x) / $h1->vx;
        $t2 = ($xs - $h2->x) / $h2->vx;
        $t3 = ($ys - $h1->y) / $h1->vy;
        $t4 = ($ys - $h2->y) / $h2->vy;
        if ($t1 < 0 || $t2 < 0 || $t3 < 0 || $t4 < 0) {
            continue;
        }
        if ($xs > 200000000000000 && $ys > 200000000000000 && $xs < 400000000000000 && $ys < 400000000000000) {
            ++$result1;
        }
    }
}
echo 'Result 1: ', $result1, PHP_EOL;

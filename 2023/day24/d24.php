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

// Soit le rocher rx, ry, rz, rvx, rvy, rvz
// Pour chaque hailstone il x, y, z, vx, vy, vz
//
// Il existe un t tel que
// x + t * vx = rx + t * rvx
// y + t * vy = ry + t * rvy
// z + t * vz = rz + t * rvz
//
// Donc
// t = (x - rx) / (rvx - vx) ou t = (rx - x) / (vx - rvx)
// t = (y - ry) / (rvy - vy) ou t = (ry - y) / (vy - rvy)
// t = (z - rz) / (rvz - vz) ou t = (rz - z) / (vz - rvz)
//
// Donc
// (x - rx) / (rvx - vx) = (y - ry) / (rvy - vy)
// (x - rx)(rvy - vy) = (y - ry)(rvx - vx)
//
// (x rvy) - (x vy) - (rx rvy) + (rx vy) = (y rvx) - (y vx) - (ry rvx) + (ry vx)
// (rx rvy) - (ry rvx) = (x rvy) - (x vy) + (rx vy) - (y rvx) + (y vx) - (ry vx)
//
// Pour une seconde hailstone x', y', z', vx', vy', vz'
// (rx rvy) - (ry rvx) = (x' rvy) - (x' vy') + (rx vy') - (y' rvx) + (y' vx') - (ry vx')
//
// Donc
// (x rvy) - (x vy) + (rx vy) - (y rvx) + (y vx) - (ry vx) = (x' rvy) - (x' vy') + (rx vy') - (y' rvx) + (y' vx') - (ry vx')
//
// (x rvy) + (rx vy) - (y rvx) - (ry vx) - (x' rvy) - (rx vy') + (y' rvx) + (ry vx') = -(x' vy') + (y' vx') + (x vy) - (y vx)
// rvy(x) + rx(vy) - rvx(y) - ry(vx) - rvy(x') - rx(vy') + rvx(y') + ry(vx') = -(x' vy') + (y' vx') + (x vy) - (y vx)
// rx(vy-vy') + ry(vx'-vx) + rvx(y'-y) + rvy(x-x') = -(x' vy') + (y' vx') + (x vy) - (y vx)
//
// Nos 4 inconnues sont donc rx, ry, rvx, rvy
// Nous pouvons donc résoudre cette équation en utilisant le système d'elimination de Gauss

$coefs = $results = [];

// https://en.wikipedia.org/wiki/Gaussian_elimination
//
// Lors de la première execution de cet algorithme sur l'input final, les résultats sont
// erronés du fait de la précision des nombres flottants.
// Une classe Fraction a donc été créée pour permettre de faire les calculs sans perte de précision
function gaussianElimination(array $coefs, array $results)
{
    $c = count($coefs);
    $coefs = array_map(static fn ($x) => array_map(static fn ($y) => new Fraction($y, 1), $x), $coefs);
    $results = array_map(static fn ($x) => new Fraction($x, 1), $results);

    for ($i = 0; $i < $c; ++$i) {
        $pivot = $coefs[$i][$i];
        for ($j = 0; $j < $c; ++$j) {
            $coefs[$i][$j] = $coefs[$i][$j]->div($pivot);
        }
        $results[$i] = $results[$i]->div($pivot);

        for ($k = 0; $k < $c; ++$k) {
            if ($k != $i) {
                $factor = $coefs[$k][$i];
                for ($j = 0; $j < $c; ++$j) {
                    $coefs[$k][$j] = $coefs[$k][$j]->sub($coefs[$i][$j]->mul($factor));
                }
                $results[$k] = $results[$k]->sub($results[$i]->mul($factor));
            }
        }
    }

    return array_map(static fn ($x) => $x->toInt(), $results);
}

class Fraction {
    private string $numerator;
    private string $denominator;

    public function __construct(int|string $numerator, int|string $denominator)
    {
        $this->numerator = (string) $numerator;
        $this->denominator = (string) $denominator;
    }

    public function mul(Fraction $other): Fraction
    {
        return new Fraction(bcmul($this->numerator, $other->numerator), bcmul($this->denominator, $other->denominator));
    }

    public function div(Fraction $other): Fraction
    {
        return new Fraction(bcmul($this->numerator, $other->denominator), bcmul($this->denominator, $other->numerator));
    }

    public function sub(Fraction $other): Fraction
    {
        return new Fraction(bcsub(bcmul($this->numerator, $other->denominator), bcmul($other->numerator, $this->denominator)), bcmul($this->denominator, $other->denominator));
    }

    public function toInt(): int
    {
        return (int) bcdiv($this->numerator, $this->denominator);
    }
}

for ($i = 0; $i < 4; ++$i) {
    $h1 = $hailstones[$i];
    $h2 = $hailstones[$i+1];
    $coefs[$i][0] = $h1->vy - $h2->vy;
    $coefs[$i][1] = $h2->vx - $h1->vx;
    $coefs[$i][2] = $h2->y - $h1->y;
    $coefs[$i][3] = $h1->x - $h2->x;
    $results[$i] = -($h2->x * $h2->vy) + ($h2->y * $h2->vx) + ($h1->x * $h1->vy) - ($h1->y * $h1->vx);
}

[$rx, $ry, $rvx, $rvz ] = gaussianElimination($coefs, $results);

// Nous pouvons utiliser la "même" équation pour trouver Z
//
// rx(vz-vz') + rz(vx'-vx) + rvx(z'-z) + rvz(x-x') = -(x' vz') + (z' vx') + (x vz) - (z vx)
//
// Nous connaissons cette fois rx et rvx donc
//
// rz(vx'-vx) + rvz(x-x') = -(x' vz') + (z' vx') + (x vz) - (z vx) - rx(vz-vz') - rvx(z'-z)

$coefs = $results = [];

for ($i = 0; $i < 2; ++$i) {
    $h1 = $hailstones[$i];
    $h2 = $hailstones[$i+1];
    $coefs[$i][0] = $h2->vx - $h1->vx;
    $coefs[$i][1] = $h1->x - $h2->x;
    $results[$i] = -($h2->x * $h2->vz) + ($h2->z * $h2->vx) + ($h1->x * $h1->vz) - ($h1->z * $h1->vx)
        - $rx * ($h1->vz - $h2->vz) - $rvx * ($h2->z - $h1->z);
}

[ $rz, $rvz ] = gaussianElimination($coefs, $results);

echo 'Result 2: ', $rx + $ry + $rz, PHP_EOL;


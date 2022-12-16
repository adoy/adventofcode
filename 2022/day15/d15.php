<?php

declare(strict_types=1);

readonly class Sensor
{
    private int $manathanDistanceFromBeacon;
    public function __construct(
        public int $x,
        public int $y,
        public int $beaconX,
        public int $beaconY
    ) {
        $this->manathanDistanceFromBeacon = abs($this->x - $this->beaconX) + abs($this->y - $this->beaconY);
    }

    public function getManathanDistanceFromBeacon(): int
    {
        return $this->manathanDistanceFromBeacon;
    }

    public function getManathanDistanceFrom(int $x, int $y): int
    {
        return abs($this->x - $x) + abs($this->y - $y);
    }

    public function getSurroundingPoints(int $manathanDistance, int $lowerBound = 0, int $higherBound = 4000000): iterable
    {
        $minY = max($lowerBound, $this->y - $manathanDistance);
        $maxY = min($higherBound, $this->y + $manathanDistance);
        for ($y = $minY; $y <= $maxY; ++$y) {
            $hd = abs(abs($this->y - $y) - $manathanDistance);
            for ($x = $this->x - $hd; $x <= $this->x + $hd; $x+=(2*$hd)) {
                if ($x >= $lowerBound && $x <= $higherBound) {
                    yield [$x, $y];
                }
                if (0 === $hd) {
                    break;
                }
            }
        }
    }
}

readonly class Map
{
    public function __construct(
        private array $sensors
    ) {
    }

    public function countPositionsNotHavingBeaconsOnLine(int $y): int
    {
        $ranges = [];
        foreach ($this->sensors as $sensor) {
            $md = $sensor->getManathanDistanceFromBeacon();

            if ($y >= $sensor->y - $md && $y <= $sensor->y + $md) {
                $hd = abs(abs($sensor->y - $y) - $md);
                if ($y === $sensor->beaconY && $sensor->beaconX >= $sensor->x - $hd && $sensor->beaconX <= $sensor->x + $hd) {
                    $ranges[] = [$sensor->x - $hd, $sensor->beaconX - 1];
                    $ranges[] = [$sensor->beaconX + 1, $sensor->x + $hd];
                } else {
                    $ranges[] = [$sensor->x - $hd, $sensor->x + $hd];
                }
            }
        }

        usort($ranges, fn($a, $b) => $a[0] <=> $b[0]);
        $beaconsFreePos = 0;
        $previous = array_shift($ranges);
        while ($range = array_shift($ranges)) {
            if ($range[0] <= $previous[1] && $range[1] > $previous[1]) {
                $previous[1] = $range[1];
            } elseif ($range[0] > $previous[1]) {
                $beaconsFreePos += $previous[1] - $previous[0] + 1;
                $previous = $range;
            }
        }
        $beaconsFreePos += $previous[1] - $previous[0] + 1;

        return $beaconsFreePos;
    }

    private function canHaveBeacon(int $x, int $y): bool
    {
        foreach ($this->sensors as $sensor) {
            if ($sensor->getManathanDistanceFromBeacon() >= $sensor->getManathanDistanceFrom($x, $y)) {
                return false;
            }
        }

        return true;
    }

    public function getTuningFrequency(int $lowerBound = 0, int $higherBound = 4000000): int
    {
        foreach ($this->sensors as $sensor) {
            $md = $sensor->getManathanDistanceFromBeacon() + 1;

            foreach ($sensor->getSurroundingPoints($md, $lowerBound, $higherBound) as [$x, $y]) {
                if ($this->canHaveBeacon($x, $y)) {
                    return $x * 4000000 + $y;
                }
            }
        }

        return -1;
    }
}

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

$sensors = [];
foreach ($input as $line) {
    sscanf($line, 'Sensor at x=%d, y=%d: closest beacon is at x=%d, y=%d', $x, $y, $bx, $by);
    $sensors[] = new Sensor($x, $y, $bx, $by);
}

$map = new Map($sensors);
printf(
    "Result 1: %d\nResult 2: %d\n",
    $map->countPositionsNotHavingBeaconsOnLine((int) $_SERVER['argv'][1] ?? 10),
    $map->getTuningFrequency(higherBound: (int) $_SERVER['argv'][2] ?? 20)
);

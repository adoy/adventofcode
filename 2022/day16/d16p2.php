<?php

declare(strict_types=1);

class Actor
{
    public bool $isMoving = false;
    public int $distanceFromDestination = 0;

    public function __construct(
        public int $position,
    ) {
    }
}

class Path
{
    public int $time = 0;
    public int $pressure = 0;
    public int $rate = 0;

    public Actor $me;
    public Actor $el;

    public function __construct(
        int $position,
        public array $destinations,
        private array $valves,
        private readonly array $distances,
    ) {
        $this->me = new Actor($position);
        $this->el = new Actor($position);
    }

    public function tick(int $duration = 1): self
    {
        $new = clone $this;
        $new->time += $duration;
        $new->pressure += $duration * $new->rate;

        return $new;
    }

    public function keepMovingMe(): Path
    {
        if (!$this->me->isMoving) {
            return $this;
        }
        if (0 === $this->me->distanceFromDestination) {
            $this->me->isMoving = false;
            $this->rate += $this->valves[$this->me->position];
        } else {
            --$this->me->distanceFromDestination;
        }

        return $this;
    }

    public function keepMovingEl(): Path
    {
        if (!$this->el->isMoving) {
            return $this;
        }
        if (0 === $this->el->distanceFromDestination) {
            $this->el->isMoving = false;
            $this->rate += $this->valves[$this->el->position];
        } else {
            --$this->el->distanceFromDestination;
        }

        return $this;
    }

    public function initMyMove(int $dest): self
    {
        $this->me->isMoving = true;
        $this->me->distanceFromDestination = $this->distances[$this->me->position][$dest] - 1;
        $this->me->position = $dest;
        $this->destinations = array_filter($this->destinations, fn ($d) => $d !== $dest);

        return $this;
    }

    public function initElMove(int $dest): self
    {
        $this->el->isMoving = true;
        $this->el->distanceFromDestination = $this->distances[$this->el->position][$dest] - 1;
        $this->el->position = $dest;
        $this->destinations = array_filter($this->destinations, fn ($d) => $d !== $dest);

        return $this;
    }

    public function __clone(): void
    {
        $this->me = clone $this->me;
        $this->el = clone $this->el;
    }
}

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

$valves = [];
$tunnels = [];
$locations = [];

foreach ($input as $key => $line) {
    if (preg_match('/Valve ([A-Z]{2}) has flow rate=([0-9]+); tunnels? leads? to valves? (.*)/', $line, $matches)) {
        $locations[$matches[1]] = $key;
        $valves[] = (int) $matches[2];
        $tunnels[] = explode(', ', $matches[3]);
    }
}

$tunnels = array_map(fn ($t) => array_map(fn ($v) => $locations[$v], $t), $tunnels);

function computeDistances(array $valves, array $tunnels): array
{
    $count = count($valves);

    // Floyd-Warshall
    $distances = array_fill(0, $count, array_fill(0, $count, PHP_INT_MAX));
    foreach ($valves as $key => $valve) {
        $distances[$key][$key] = 0;
        foreach ($tunnels[$key] as $tunnel) {
            $distances[$key][$tunnel] = 1;
        }
    }

    for ($k = 0; $k < count($valves); ++$k) {
        for ($i = 0; $i < count($valves); ++$i) {
            for ($j = 0; $j < count($valves); ++$j) {
                if ($distances[$i][$k] + $distances[$k][$j] < $distances[$i][$j]) {
                    $distances[$i][$j] = $distances[$i][$k] + $distances[$k][$j];
                }
            }
        }
    }

    return $distances;
}

function permutations(array $destinations): array
{
    $permutations = [];
    foreach ($destinations as $me) {
        $others = array_filter($destinations, fn ($d) => $d !== $me);
        foreach ($others as $other) {
            $permutations[] = [$me, $other];
        }
    }

    return $permutations;
}

$count = count($valves);
$distances = computeDistances($valves, $tunnels);
$destinations = array_keys(array_filter($valves, fn ($x) => $x > 0));

$bestPath = null;
$path = new Path($locations['AA'], $destinations, $valves, $distances);

$queue = new SplQueue();
$queue->push($path);
const TIME = 26;
while (count($queue) && $path = $queue->pop()) {
    assert($path instanceof Path);
    if ($path->time >= TIME) {
        if (null === $bestPath || $bestPath->pressure < $path->pressure) {
            $bestPath = $path;
        }
        continue;
    }
    $remaining = TIME - $path->time - 1;

    if ($path->me->isMoving && $path->el->isMoving) {
        $queue[] = $path->tick()->keepMovingMe()->keepMovingEl();
    } elseif ($path->me->isMoving) {
        $destinations = array_filter($path->destinations, fn ($d) => $distances[$path->el->position][$d] <= $remaining);
        if ($destinations) {
            foreach ($destinations as $destination) {
                $queue[] = $path->tick()->keepMovingMe()->initElMove($destination);
            }
        } else {
            $queue[] = $path->tick()->keepMovingMe();
        }
    } elseif ($path->el->isMoving) {
        $destinations = array_filter($path->destinations, fn ($d) => $distances[$path->me->position][$d] <= $remaining);
        if ($destinations) {
            foreach ($destinations as $destination) {
                $queue[] = $path->tick()->initMyMove($destination)->keepMovingEl();
            }
        } else {
            $queue[] = $path->tick()->keepMovingEl();
        }
    } else {
        $meDestinations = array_filter($path->destinations, fn ($d) => $distances[$path->me->position][$d] <= $remaining);
        $elDestinations = array_filter($path->destinations, fn ($d) => $distances[$path->el->position][$d] <= $remaining);
        $destinations = array_unique(array_merge($meDestinations, $elDestinations));

        if (0 === count($destinations)) {
            $queue[] = $path->tick(TIME - $path->time);
            continue;
        }

        foreach (permutations($destinations) as [ $myDest, $elDest ]) {
            if (in_array($myDest, $meDestinations) && in_array($elDest, $elDestinations)) {
                $queue[] = $path->tick()->initMyMove($myDest)->initElMove($elDest);
            }
        }
    }
}
printf("Result 2 en %d: %d\n", $bestPath->time, $bestPath->pressure);

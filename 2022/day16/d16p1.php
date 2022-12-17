<?php

declare(strict_types=1);

class Path
{
    public int $time = 0;
    public int $pressure = 0;
    public int $rate = 0;

    public function __construct(
        public int $position,
        public array $destinations,
        private array $valves,
        private readonly array $distances,
    ) {
    }

    public function moveToValveAndOpen(int $dest): self
    {
        $new = clone $this;
        $new->position = $dest;
        $new->time += $duration = $this->distances[$this->position][$dest] + 1;
        $new->pressure += $duration * $this->rate;
        $new->rate += $this->valves[$dest];
        $new->destinations = array_filter($new->destinations, fn ($d) => $d !== $dest);

        return $new;
    }

    public function wait(int $duration = 1): self
    {
        $new = clone $this;
        $new->time += $duration;
        $new->pressure += $duration * $new->rate;

        return $new;
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

$count = count($valves);
$distances = computeDistances($valves, $tunnels);
$destinations = array_keys(array_filter($valves, fn ($x) => $x > 0));

$bestPath = null;
$initialPath = new Path($locations['AA'], $destinations, $valves, $distances);
$queue = new SplQueue();
$queue->push($initialPath);

while (count($queue) && $path = $queue->pop()) {
    if ($path->time >= 30) {
        if (null === $bestPath) {
            $bestPath = $path;
        } elseif ($bestPath->pressure < $path->pressure) {
            $bestPath = $path;
        }
        continue;
    }

    $remaining = 30 - $path->time - 1;
    $destinations = array_filter($path->destinations, fn ($d) => $distances[$path->position][$d] <= $remaining);

    if (0 === count($destinations)) {
        $queue[] = $path->wait(30 - $path->time);
        continue;
    }

    foreach ($destinations as $dest) {
        $queue[] = $path->moveToValveAndOpen($dest);
    }
}
exit('Result 1: ' . $bestPath->pressure . PHP_EOL);

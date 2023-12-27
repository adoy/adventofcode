<?php

declare(strict_types=1);

// https://www.geeksforgeeks.org/introduction-and-implementation-of-kargers-algorithm-for-minimum-cut/
function minCutKarger(Graph $graph, int $c = 3): int
{
    $randomizer = new Random\Randomizer();
    $ce = count($edges = $graph->getEdges());
    $parent = $rank = [];

    $find = function (string $vertice) use (&$parent, &$find): string {
        if ($vertice === $parent[$vertice]) {
            return $vertice;
        }

        return $parent[$vertice] = $find($parent[$vertice]);
    };

    // https://www.geeksforgeeks.org/union-by-rank-and-path-compression-in-union-find-algorithm/
    $union = function (string $u, string $v) use (&$parent, &$rank, &$find) {
        $u = $find($u);
        $v = $find($v);
        if ($u != $v) {
            if ($rank[$u] < $rank[$v]) {
                [ $u, $v ] = [ $v, $u ];
            }
            $parent[$v] = $u;

            if ($rank[$u] == $rank[$v]) {
                ++$rank[$u];
            }
        }
    };

    do {
        $edges = $randomizer->shuffleArray($edges);
        $vertices = count($graph->getVertices());
        $rank = array_combine($graph->getVertices(), array_fill(0, $vertices, 0));
        $parent = array_combine($graph->getVertices(), $graph->getVertices());
        $n = 0;

        while ($vertices > 2) {
            [ $u, $v ] = $edges[$n++];

            $set1 = $find($u);
            $set2 = $find($v);

            if ($set1 != $set2) {
                $union($set1, $set2);
                --$vertices;
            }
        }

        $minCut = 0;
        foreach ($graph->getEdges() as [ $u, $v ]) {
            $set1 = $find($u);
            $set2 = $find($v);
            $minCut += ($set1 != $set2);
        }
    } while ($c !== $minCut);

    return array_product(array_count_values($parent));
}

class Graph
{
    private array $vertices = [];
    private array $edges = [];

    public function addEdge(string $e1, string $e2)
    {
        $this->edges[] = [ $this->addVertice($e1), $this->addVertice($e2) ];
    }

    public function getEdges(): array
    {
        return $this->edges;
    }

    public function getVertices(): array
    {
        return $this->vertices;
    }

    private function addVertice(string $vertice): string
    {
        return $this->vertices[$vertice] = $vertice;
    }
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$graph = new Graph();

foreach ($lines as $line) {
    $edges = explode(' ', str_replace(':', '', trim($line)));
    $e1 = array_shift($edges);
    foreach ($edges as $e2) {
        $graph->addEdge($e1, $e2);
    }
}

echo 'Result 1: ', minCutKarger($graph), PHP_EOL;

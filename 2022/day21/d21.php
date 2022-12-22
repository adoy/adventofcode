<?php

declare(strict_types=1);

class Graph
{
    private array $cache = [];

    public function __construct(
        private readonly array $definition
    ) {
    }

    public function getNode(string $name): Value
    {
        if (!isset($this->cache[$name])) {
            if (ctype_digit($this->definition[$name])) {
                return $this->cache[$name] = new Number((int) $this->definition[$name]);
            }
            [ $leftName, $operator, $rightName ] = explode(' ', $this->definition[$name]);

            return $this->cache[$name] = new Operation($this->getNode($leftName), $operator, $this->getNode($rightName));
        }

        return $this->cache[$name];
    }
}

interface Value
{
    public function getValue(): ?int;

    public function solve(int $result = null): int;
}

class Number implements Value
{
    public function __construct(
        public ?int $value
    ) {
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function solve(int $result = null): int
    {
        return $this->value ?? $result;
    }
}

class Operation implements Value
{
    private ?int $value = null;

    public function __construct(
        public Value $left,
        public string $operator,
        public Value $right
    ) {
    }

    public function getValue(): ?int
    {
        $left = $this->left->getValue();
        $right = $this->right->getValue();
        if (null === $left || null === $right) {
            return null;
        }

        return match ($this->operator) {
            '+' => $left + $right,
            '-' => $left - $right,
            '*' => $left * $right,
            '/' => $left / $right,
        };
    }

    public function solve(int $result = null): int
    {
        $left = $this->left->getValue();
        $right = $this->right->getValue();

        $unknownNode = match (true) {
            null === $left => $this->left,
            null === $right => $this->right,
        };

        $knownNodeValue = $left ?? $right;
        if (null === $result) {
            return $unknownNode->solve($knownNodeValue);
        }

        return $unknownNode->solve(match ($this->operator) {
            '+' => $result - $knownNodeValue,
             '-' => ($unknownNode === $this->left) ? $result + $knownNodeValue : $knownNodeValue - $result,
             '*' => $result / $knownNodeValue,
             '/' => ($unknownNode === $this->left) ? $result * $knownNodeValue : $result / $knownNodeValue,
        });
    }
}

$input = explode(PHP_EOL, trim(file_get_contents('php://stdin')));
$def = [];
foreach ($input as $line) {
    [ $name, $value ] = explode(': ', $line);
    $def[$name] = $value;
}

$graph = new Graph($def);
$res1 = $graph->getNode('root')->getValue();
$graph->getNode('humn')->value = null;
$res2 = $graph->getNode('root')->solve();

printf("Result 1: %d\nResult 2: %d\n", $res1, $res2);

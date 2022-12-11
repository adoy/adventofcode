<?php

declare(strict_types=1);

class Monkey
{
    private array $items;
    private array $operation = [];
    private int $test = 0;
    private int $throwToTrue = 0;
    private int $throwToFalse = 0;
    private int $inspectedItemsCount = 0;

    public function __construct(string $data)
    {
        $data = explode(PHP_EOL, $data);
        $this->items = array_map(fn ($v) => (int) $v, explode(', ', explode(':', $data[1])[1]));
        sscanf($data[2], '  Operation: new = old %s %s', $this->operation['operator'], $this->operation['value']);
        sscanf($data[3], '  Test: divisible by %d', $this->test);
        sscanf($data[4], '    If true: throw to monkey %d', $this->throwToTrue);
        sscanf($data[5], '    If false: throw to monkey %d', $this->throwToFalse);
    }

    public function inspectItems(array $monkeys, callable $levelManagementTechnique): void
    {
        while ($item = array_shift($this->items)) {
            ++$this->inspectedItemsCount;
            switch ($this->operation['operator']) {
                case '+':
                    $item += ('old' == $this->operation['value']) ? $item : (int) $this->operation['value'];
                    break;
                case '*':
                    $item *= ('old' == $this->operation['value']) ? $item : (int) $this->operation['value'];
                    break;
            }
            $item = $levelManagementTechnique($item);
            $monkeys[(0 == $item % $this->test) ? $this->throwToTrue : $this->throwToFalse]->items[] = $item;
        }
    }

    public function getInspectedItemsCount(): int
    {
        return $this->inspectedItemsCount;
    }

    public function getTestValue(): int
    {
        return $this->test;
    }
}

class MonkeyCollection
{
    private array $monkeys = [];

    public function __construct(string $initData)
    {
        $input = explode(PHP_EOL . PHP_EOL, $initData);
        foreach ($input as $data) {
            $this->monkeys[] = new Monkey($data);
        }
    }

    public function waitRounds(int $n, callable $callback): int
    {
        $monkeys = array_map(fn ($m) => clone $m, $this->monkeys);
        for ($i = 0; $i < $n; ++$i) {
            foreach ($monkeys as $monkey) {
                $monkey->inspectItems($monkeys, $callback);
            }
        }
        $count = array_map(fn ($x) => $x->getInspectedItemsCount(), $monkeys);
        rsort($count);

        return $count[0] * $count[1];
    }

    public function getMonkeysProduct(): int
    {
        // If the product was bigger than PHP_INT_MAX, we would have to get the LCM of all monkeys test values
        // (but all test values looks like prime numbers so the product is probably already the LCM)
        return array_reduce($this->monkeys, fn ($c, Monkey $m) => $c * $m->getTestValue(), 1);
    }
}

$input = trim(file_get_contents('php://stdin'));

$monkeys = new MonkeyCollection($input);
$product = $monkeys->getMonkeysProduct();

printf(
    "Result 1: %d\nResult 2: %d\n",
    $monkeys->waitRounds(20, fn ($x) => floor($x/3)),
    $monkeys->waitRounds(10000, fn ($x) => $x % $product)
);

<?php

declare(strict_types=1);

class Stacks
{
    private $stacks = [];

    public function __construct(string $init) {
        $init = explode(PHP_EOL, $init);
        $stackCount = (int) ceil(strlen(array_pop($init))/4);
        while ($values = array_pop($init)) {
            for ($i = 1; $i < strlen($values); $i+= 4) {
                if (ctype_alnum($values[$i])) {
                    $this->stacks[($i-1)/4][] = $values[$i];
                }
            }
        }
    }

    public function move(Instruction $instruction, bool $keepOrder = false): void {
        $batch = [];
        for ($i = 0; $i < $instruction->count; $i++) {
            $batch[] = array_pop($this->stacks[$instruction->from]);
        }

        $this->stacks[$instruction->to] = array_merge(
            $this->stacks[$instruction->to],
            $keepOrder ? array_reverse($batch) : $batch
        );
    }

    public function getTopCrates(): string
    {
        return implode('', array_map(fn ($stack) => end($stack), $this->stacks));
    }
}

readonly class Instruction {
    public function __construct(
        public int $count,
        public int $from,
        public int $to,
    ) {}
}

class InstructionIterator implements IteratorAggregate
{
    private array $instructions;
    public function __construct(string $instructions) {
       $this->instructions = explode(PHP_EOL, $instructions);
    }
    public function getIterator(): Traversable
    {
        foreach ($this->instructions as $instruction) {
            if (preg_match('/move (\d+) from (\d+) to (\d+)/', $instruction, $matches)) {
                yield new Instruction(
                    (int) $matches[1],
                    (int) $matches[2] - 1,
                    $matches[3] - 1
                );
            }
        }
    }
}
$input = file_get_contents('php://stdin');
[ $stacks, $instructions ] = explode(PHP_EOL . PHP_EOL, $input);
$stacksPart1 = new Stacks($stacks);
$stacksPart2 = clone $stacksPart1;
foreach (new InstructionIterator($instructions) as $instruction) {
    $stacksPart1->move($instruction, keepOrder: false);
    $stacksPart2->move($instruction, keepOrder: true);
}
echo 'Result 1: ', $stacksPart1->getTopCrates(), PHP_EOL;
echo 'Result 2: ', $stacksPart2->getTopCrates(), PHP_EOL;

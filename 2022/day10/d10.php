<?php

declare(strict_types=1);

class CRT
{
    private array $display;

    public function __construct(int $height = 6, int $width = 40)
    {
        $this->display = array_fill(0, $height, str_repeat(' ', $width));
    }

    public function draw(int $cycle, int $x): void
    {
        $col  = ($cycle - 1) % 40;
        $line = floor(($cycle - 1) / 40);
        if (abs($x - $col) <= 1) {
            $this->display[$line][$col] = '#';
        }
    }

    public function __toString(): string
    {
        return implode(PHP_EOL, $this->display);
    }
}

class CPU
{
    private int $x = 1;
    private int $cycle = 0;
    private array $signalStrengths = [];

    public function __construct(
        private CRT $crt
    ) {
    }

    public function run(array $program): void
    {
        foreach ($program as $instruction) {
            $this->execute($instruction);
        }
    }

    public function getSignalStrengths(): array
    {
        return $this->signalStrengths;
    }

    private function execute(string $instruction): void
    {
        switch ($instruction) {
            case 'noop':
                $this->tick();
                break;
            default:
                [ , $v ] = explode(' ', $instruction);
                $this->tick();
                $this->tick();
                $this->x += (int) $v;
        }
    }

    private function getSignalStrength(): int
    {
        return $this->x * $this->cycle;
    }

    private function tick(): void
    {
        ++$this->cycle;
        $this->crt->draw($this->cycle, $this->x);
        if (0 == (($this->cycle - 20) % 40)) {
            $this->signalStrengths[] = $this->getSignalStrength();
        }
    }
}

$input = trim(file_get_contents('php://stdin'));
$instructions = explode(PHP_EOL, $input);

$cpu = new CPU($crt = new CRT());
$cpu->run($instructions);
printf("Result 1: %d\nResult 2: \n%s\n", array_sum($cpu->getSignalStrengths()), $crt);

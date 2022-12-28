<?php

declare(strict_types=1);

class Rope
{
    private Knot $head;
    private Knot $tail;

    public function __construct(int $length)
    {
        $this->tail = $this->head = new Knot();
        for ($i = 1; $i < $length; ++$i) {
            $this->head = new Knot($this->head);
        }
    }

    public function move(string $direction, int $distance): void
    {
        $this->head->move(
            match ($direction) {
                'L' => -1, 'R' => 1, default => 0
            },
            match ($direction) {
                'U' => -1, 'D' => 1, default => 0
            },
            $distance
        );
    }

    public function getTailVisitedPositionCount(): int
    {
        return $this->tail->getVisitedPositionCount();
    }
}

class Knot
{
    private int $x = 0;
    private int $y = 0;
    private ?Knot $next = null;
    private array $positions = [ '0,0' => true ];

    public function __construct(?self $next = null)
    {
        $this->next = $next;
    }

    public function move(int $h, int $v, int $distance = 1): void
    {
        do {
            $this->positions[($this->x += $h) . ',' . ($this->y += $v)] = true;
            if ($this->next) {
                $this->pullToward($this->next);
            }
        } while (--$distance > 0);
    }

    private function pullToward(Knot $other): void
    {
        if (!$this->isTouching($other)) {
            $other->move($this->x <=> $other->x, $this->y <=> $other->y);
        }
    }

    private function isTouching(Knot $other): bool
    {
        return abs($this->x - $other->x) <= 1 && abs($this->y - $other->y) <= 1;
    }

    public function getVisitedPositionCount(): int
    {
        return count($this->positions);
    }
}

$input = trim(file_get_contents('php://stdin'));
$instructions = explode(PHP_EOL, $input);
$smallRope = new Rope(2);
$longRope = new Rope(10);
foreach ($instructions as $instruction) {
    [ $direction, $distance ] = explode(' ', $instruction);
    $smallRope->move($direction, (int) $distance);
    $longRope->move($direction, (int) $distance);
}
printf("Result 1: %d\nResult 2: %d\n", $smallRope->getTailVisitedPositionCount(), $longRope->getTailVisitedPositionCount());

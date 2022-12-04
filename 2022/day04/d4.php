<?php

declare(strict_types=1);

class Section
{
    public function __construct(
        private int $start,
        private int $end,
    ) {
    }

    public function contains(Section $section): bool
    {
        return $this->start <= $section->start && $this->end >= $section->end;
    }

    public function overlap(Section $section): bool
    {
        return $this->start <= $section->end && $this->end >= $section->start;
    }
}

class Input implements IteratorAggregate {
    public function __construct(
        private string $fn,
    ) {
    }

    public function getIterator(): Generator
    {
        $handle = fopen($this->fn, 'rb');
        while (($line = fgets($handle)) !== false) {
            preg_match_all('/(\d+)-(\d+),(\d+)-(\d+)/', $line, $matches);
            yield [
                new Section((int) $matches[1][0], (int) $matches[2][0]),
                new Section((int) $matches[3][0], (int) $matches[4][0])
            ];
        }
        fclose($handle);
    }
}

$res1 = $res2 = 0;
foreach (new Input('php://stdin') as [$section1, $section2]) {
    $res1 += $section1->contains($section2) || $section2->contains($section1) ? 1 : 0;
    $res2 += $section1->overlap($section2) || $section2->overlap($section1) ? 1 : 0;
}
printf("Result 1: %d\nResult 2: %d\n", $res1, $res2);


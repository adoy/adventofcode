<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$input = explode(PHP_EOL, $input);

class Range
{
    public function __construct(
        public readonly int $start,
        public readonly int $end
    ) {
    }
}

[ , $seedsList ] = explode(':', array_shift($input));
$seedsList = array_map(intval(...), array_filter(explode(' ', $seedsList)));
$seedsRange = array_map(fn ($x) => new Range($x[0], $x[0] + $x[1] - 1), array_chunk($seedsList, 2));

$maps = [];
foreach (array_filter($input) as $line) {
    if (preg_match('/(.*):/', $line, $matches)) {
        $key = $matches[1];
        $maps[$key] = [];
    } else {
        $maps[$key][] = array_map(intval(...), explode(' ', $line));
    }
}

foreach ($maps as $name => $map) {
    $newSeedsRange = [];
    while ($seedRange = array_shift($seedsRange)) {
        foreach ($map as [ $drs, $srs, $l ]) {
            $sourceRange = new Range($srs, $srs + $l - 1);
            $offset = $drs - $srs;

            if ($seedRange->start >= $sourceRange->start && $seedRange->end <= $sourceRange->end) {
                $newSeedsRange[] = $new = new Range($seedRange->start + $offset, $seedRange->end + $offset);
                continue 2;
            } elseif ($sourceRange->start >= $seedRange->start && $sourceRange->end <= $seedRange->end) {
                $seedsRange[] = new Range($seedRange->start, $sourceRange->start - 1);
                $newSeedsRange[] = new Range($sourceRange->start + $offset, $sourceRange->end + $offset);
                $seedsRange[] = new Range($sourceRange->end + 1, $seedRange->end);
                continue 2;
            } elseif ($seedRange->start < $sourceRange->end && $seedRange->end > $sourceRange->end) {
                $newSeedsRange[] = new Range($seedRange->start + $offset, $sourceRange->end + $offset);
                $seedsRange[] = new Range($sourceRange->end + 1, $seedRange->end);
                continue 2;
            } elseif ($seedRange->start < $sourceRange->start && $seedRange->end > $sourceRange->start) {
                $seedsRange[] =  new Range($seedRange->start, $sourceRange->start - 1);
                $newSeedsRange[] = new Range($sourceRange->start + $offset, $seedRange->end + $offset);
                continue 2;
            }
        }
        $newSeedsRange[] = $seedRange;
    }
    $seedsRange = $newSeedsRange;
}

echo 'Result 2: ', min(array_map(fn ($range) => $range->start, $seedsRange)), PHP_EOL;

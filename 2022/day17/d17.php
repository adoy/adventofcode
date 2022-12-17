<?php

declare(strict_types=1);

const S1 = [ '####' ];
const S2 = [ ' # ', '###', ' # ' ];
const S3 = [ '  #', '  #', '###' ];
const S4 = [ '#', '#', '#', '#' ];
const S5 = [ '##', '##' ];

class Rock
{
    public bool $isStopped = false;
    public int $width;
    public int $height;
    public ?array $points = null;

    public function __construct(
        private Chamber $chamber,
        public int $x,
        public int $y,
        public array $src,
        private int $type = 0
    ) {
        $this->width = strlen($this->src[0]);
        $this->height = count($this->src);
        $this->y += $this->height - 1;
    }

    public function getHash(): string
    {
        return $this->type . ':' . $this->x;
    }

    public function down(): self
    {
        $down = $this->getDownCopy();
        if ($this->chamber->overlap($down) || ($this->y - $this->height + 1 <= 0)) {
            $this->isStopped = true;

            return $this;
        }

        return $down;
    }

    public function left(): self
    {
        $left = $this->getLeftCopy();

        return ($this->chamber->overlap($left) || $this->x - 1 < 0) ? $this : $left;
    }

    public function right(): self
    {
        $right = $this->getRightCopy();

        return ($this->chamber->overlap($right) || ($this->x + $this->width + 1  > $this->chamber->width)) ? $this : $right;
    }

    public function getDownCopy(int $offset = -1): self
    {
        $clone = clone $this;
        $clone->y += $offset;

        return $clone;
    }

    public function getRightCopy(): self
    {
        $clone = clone $this;
        ++$clone->x;

        return $clone;
    }

    public function getLeftCopy(): self
    {
        $clone = clone $this;
        --$clone->x;

        return $clone;
    }

    public function getPoints(): array
    {
        if (null === $this->points) {
            $this->points = [];
            foreach ($this->src as $y => $line) {
                foreach (str_split($line) as $x => $char) {
                    if ('#' === $char) {
                        $this->points[] = [$this->x + $x, $this->y - $y];
                    }
                }
            }
        }

        return $this->points;
    }

    public function overlap(Rock $rock): bool
    {
        return (bool) array_uintersect($this->getPoints(), $rock->getPoints(), fn ($a, $b) => $a <=> $b);
    }

    public function __clone(): void
    {
        $this->points = null;
    }
}

class Chamber
{
    public const ROCKS = [ S1, S2, S3, S4, S5 ];
    private int $rockType = 4;
    private int $nextWind = 0;

    public int $top = 0;
    public array $rocks = [];

    public array $hashes = [];
    public int   $sim    = 10;
    public ?int  $loop   = null;

    private ?Rock $nextRock = null;

    public function __construct(
        public int $width,
        private string $winds
    ) {
    }

    private function createNextRockStructure(): array
    {
        return self::ROCKS[($this->rockType = ++$this->rockType % 5)];
    }

    public function overlap(Rock $s): bool
    {
        foreach ($this->rocks as $rock) {
            if ($rock->overlap($s)) {
                return true;
            }
        }

        return false;
    }

    private function dropRock(): void
    {
        if ($this->nextRock) {
            $rock = $this->nextRock;
        } else {
            $rock = new Rock($this, 2, $this->top + 3, $this->createNextRockStructure(), $this->rockType);
            while (!$rock->isStopped) {
                switch ($this->getWindDirection()) {
                    case '<':
                        $rock = $rock->left();
                        break;
                    case '>':
                        $rock = $rock->right();
                        break;
                }
                $rock = $rock->down();
            }
        }
        $this->top = max($this->top, $rock->y + 1);
        array_unshift($this->rocks, $rock);

        $i = 0;
        $hash = '';
        foreach ($this->rocks as $rock) {
            if (++$i > $this->sim) {
                break;
            }
            $hash .= $rock->getHash() . '|';
        }

        if (!$this->loop) {
            foreach ($this->hashes as $key => $h) {
                if ($h === $hash) {
                    $this->loop = $key+1;
                    break;
                }
            }
            array_unshift($this->hashes, $hash);
        }

        if ($this->loop) {
            $this->nextRock = $this->rocks[$this->loop-1]->getDownCopy($this->rocks[0]->y - $this->rocks[$this->loop]->y);
        }
    }

    private function getWindDirection(): string
    {
        $nextWind = $this->winds[$this->nextWind];
        $this->nextWind = ($this->nextWind + 1) % strlen($this->winds);

        return $nextWind;
    }

    public function dropRocks(int $n): void
    {
        for ($n; !$this->loop && $n > 0; --$n) {
            $this->dropRock();
        }
        if ($n) {
            $base = $this->top;
            $add = [];
            for ($i = 0; $i < $this->loop; ++$i) {
                $this->dropRock();
                $add[$i] = $this->top - $base;
            }

            $this->top = (int) ($base + floor($n / $this->loop) * $add[$this->loop-1] + $add[($n % $this->loop)-1]);
        }
    }
}

$chamber1 = new Chamber(7, trim(file_get_contents('php://stdin')));
$chamber2 = clone $chamber1;

$chamber1->dropRocks(2022);
echo $chamber1->top, PHP_EOL;

$chamber2->dropRocks(1000000000000);
echo $chamber2->top, PHP_EOL;

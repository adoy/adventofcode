<?php

declare(strict_types=1);

class Folder implements IteratorAggregate
{
    private array $files = [];

    public function __construct(
        public readonly ?Folder $parent = null
    ) {
    }

    public function addFolder(string $name): Folder
    {
        if (!isset($this->files[$name])) {
            $this->files[$name] = new Folder($this);
        }

        return $this->files[$name];
    }

    public function addFile(string $name, int $size): void
    {
        $this->files[$name] = $size;
    }

    public function getSize(): int
    {
        return array_reduce($this->files, fn ($c, $f) => $c + ($f instanceof Folder ? $f->getSize() : $f));
    }

    public function getIterator(): Traversable
    {
        yield from $this->files;
    }
}

class RecursiveFoldeIterator implements IteratorAggregate
{
    public function __construct(private readonly Folder $folder)
    {
    }

    public function getIterator(): Iterator
    {
        yield $this->folder;
        foreach ($this->folder as $key => $file) {
            if ($file instanceof Folder) {
                yield from new RecursiveFoldeIterator($file);
            }
        }
    }
}

$cwd = $root = new Folder();
$input = explode(PHP_EOL, trim(file_get_contents('php://stdin')));
foreach ($input as $line) {
    $tokens = explode(' ', $line);
    if ('$' == $tokens[0] && 'cd' == $tokens[1]) {
        $cwd = match ($tokens[2]) {
            '/' => $root,
            '..' => $cwd?->parent ?? $root,
            default => $cwd->addFolder($tokens[2]),
        };
    } elseif ('$' != $tokens[0]) {
        if ('dir' == $tokens[0]) {
            $cwd->addFolder($tokens[1]);
        } else {
            $cwd->addFile($tokens[1], (int) $tokens[0]);
        }
    }
}

$res1 = 0;
$res2 = 70000000;
$requiredSize = 30000000 - (70000000 - $root->getSize());

foreach (new RecursiveFoldeIterator($root) as $name => $folder) {
    if ($folder->getSize() < 100000) {
        $res1 += $folder->getSize();
    }
    if ($folder->getSize() > $requiredSize) {
        $res2 = min($res2, $folder->getSize());
    }
}

printf("Result 1: %d\nResult 2: %d\n", $res1, $res2);

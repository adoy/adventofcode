<?php

declare(strict_types=1);

class WorkflowCollection
{
    public array $workflows = [];

    public function add(Workflow $workflow): void
    {
        $this->workflows[$workflow->name] = $workflow;
    }

    public function execute(array $variables): string
    {
        $workflow = $this->workflows['in'];

        while (true) {
            foreach ($workflow->rules as $rule) {
                $var = $variables[$rule[1]];
                $op = $rule[2];
                $val = (int) $rule[3];
                $next = $rule[4];

                if ('<' === $op && $var < $val) {
                    switch ($next) {
                        case 'A':
                        case 'R':
                            return $next;
                        default:
                            $workflow = $this->workflows[$next];
                            continue 3;
                    }
                }
                if ('>' === $op && $var > $val) {
                    switch ($next) {
                        case 'A':
                        case 'R':
                            return $next;
                        default:
                            $workflow = $this->workflows[$next];
                            continue 3;
                    }
                }
            }

            switch ($workflow->default) {
                case 'A':
                case 'R':
                    return $workflow->default;
                default:
                    $workflow = $this->workflows[$workflow->default];
            }
        }
    }
}

class Workflow
{
    public readonly string $name;
    public readonly string $default;
    public readonly array $rules;

    public function __construct(string $def)
    {
        if (!preg_match('/([A-Za-z]+){(.*),([A-Za-z0-9]+)}/', $def, $matches)) {
            throw new \Exception('Invalid workflow definition: ' . $def);
        }
        $this->name = $matches[1];
        $this->default = $matches[3];

        $rules = [];
        foreach (explode(',', $matches[2]) as $rule) {
            if (!preg_match('/(.*)([><])(.*):(.*)/', $rule, $matches)) {
                exit('ICI');
            }
            $rules[] = $matches;
        }
        $this->rules = $rules;
    }
}

class Range
{
    public function __construct(
        public readonly int $min,
        public readonly int $max
    ) {
    }

    public function acceptedByRule(array $rule): self
    {
        $op = $rule[2];
        $val = (int) $rule[3];

        return match ($op) {
            '<' => new self($this->min, min($this->max, $val - 1)),
            '>' => new self(max($this->min, $val + 1), $this->max),
        };
    }

    public function notAcceptedByRule(array $rule): self
    {
        $op = $rule[2];
        $val = (int) $rule[3];

        return match ($op) {
            '<' => new self(max($this->min, $val), $this->max),
            '>' => new self($this->min, min($this->max, $val)),
        };
    }
}

class Ranges
{
    public Range $x;
    public Range $m;
    public Range $a;
    public Range $s;

    public function __construct(int $xmin, int $xmax, int $mmin, int $mmax, int $amin, int $amax, int $smin, int $smax)
    {
        $this->x = new Range(1, 4000);
        $this->m = new Range(1, 4000);
        $this->a = new Range(1, 4000);
        $this->s = new Range(1, 4000);
    }

    public function acceptedByRule(array $rule): self
    {
        $var = $rule[1];
        $result = clone $this;

        $result->x = 'x' === $var ? $this->x->acceptedByRule($rule) : $this->x;
        $result->m = 'm' === $var ? $this->m->acceptedByRule($rule) : $this->m;
        $result->a = 'a' === $var ? $this->a->acceptedByRule($rule) : $this->a;
        $result->s = 's' === $var ? $this->s->acceptedByRule($rule) : $this->s;

        return $result;
    }

    public function notAcceptedByRule(array $rule): self
    {
        $var = $rule[1];
        $result = clone $this;

        $result->x = 'x' === $var ? $this->x->notAcceptedByRule($rule) : $this->x;
        $result->m = 'm' === $var ? $this->m->notAcceptedByRule($rule) : $this->m;
        $result->a = 'a' === $var ? $this->a->notAcceptedByRule($rule) : $this->a;
        $result->s = 's' === $var ? $this->s->notAcceptedByRule($rule) : $this->s;

        return $result;
    }

    public function product(): int
    {
        return ($this->x->max - $this->x->min + 1) * ($this->m->max - $this->m->min + 1) * ($this->a->max - $this->a->min + 1) * ($this->s->max - $this->s->min + 1);
    }
}

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$workflows = new WorkflowCollection();
$variables = [];
$result1 = $result2 = 0;
foreach ($lines as $line) {
    switch ($line[0] ?? null) {
        case null:
            $queue = new SplQueue();
            $queue->enqueue([ 'in', new Ranges(1, 4000, 1, 4000, 1, 4000, 1, 4000) ]);
            while (!$queue->isEmpty()) {
                $item = $queue->dequeue();
                [ $res, $ranges ] = $item;
                switch ($res) {
                    case 'R':
                        continue 2;
                    case 'A':
                        $result2 += $ranges->product();
                        continue 2;
                    default:
                        $workflow = $workflows->workflows[$res];
                        foreach ($workflow->rules as $rule) {
                            $queue->enqueue([$rule[4], $x = $ranges->acceptedByRule($rule)]);
                            $ranges = $ranges->notAcceptedByRule($rule);
                        }
                        $queue->enqueue([$workflow->default, $ranges]);
                }
            }
            break;
        case '{':
            foreach (explode(',', substr($line, 1, -1)) as $defintion) {
                $x = explode('=', $defintion);
                $variables[$x[0]] = (int) $x[1];
            }
            if ('A' === $workflows->execute($variables)) {
                $result1 += array_sum($variables);
            }
            break;
        default:
            $workflows->add(new Workflow($line));
            break;
    }
}

echo 'Result 1: ', $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

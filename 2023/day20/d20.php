<?php

declare(strict_types=1);

$input = trim(file_get_contents('php://stdin'));
$lines = explode(PHP_EOL, $input);

$modules = [];

$map = [];
foreach ($lines as $line) {
    if (!$line || '#' === $line[0]) {
        continue;
    }
    [ $src, $dst ] = explode(' -> ', trim($line));
    $dst = explode(', ', $dst);
    switch ($src[0]) {
        case '%':
            $modules[$name = substr($src, 1)] = [ '%', $dst, false ];
            break;
        case '&':
            $modules[$name = substr($src, 1)] = [ '&', $dst, [] ];
            break;
        default:
            $modules[$name = $src] = [ $src, $dst ];
            break;
    }
    foreach ($dst as $d) {
        $map[$d][] = $name;
    }
}
assert(isset($map['rx']));

foreach ($modules as $name => $def) {
    if ('&' === $def[0]) {
        $modules[$name][2] = array_fill_keys($map[$name], false);
    }
}

foreach ($map['rx'] as $rx) {
    $target = $modules[$rx][2];
}

$c = [ 'low' => 0, 'high' => 0, 'total' => 0 ];
$queue = new SplQueue();
$result = [];
$result1 = $result2 = null;
for ($i = 0; !$result1 || !$result2; ++$i) {
    ++$c['low'];
    ++$c['total'];
    foreach ($modules['broadcaster'][1] as $dest) {
        $queue->enqueue([ 'broadcaster', $dest, false ]);
    }
    while (!$queue->isEmpty()) {
        [ $from, $current, $high ] = $queue->dequeue();
        if (isset($target[$current])) {
            if (!$high) {
                if ($target[$current]) {
                    $result[] = $i - $target[$current];
                    unset($target[$current]);
                    if (empty($target)) {
                        $lcm = array_pop($result);
                        $result2 = (int) array_reduce($result, gmp_lcm(...), $lcm);
                    }
                } else {
                    $target[$current] = $i;
                }
            }
        }
        ++$c['total'];
        ++$c[$high ? 'high' : 'low'];

        $modules[$current] ??= [ $current ];
        $m = &$modules[$current];
        switch ($m[0]) {
            case '%':
                if (!$high) {
                    $m[2] = !$m[2];
                    foreach ($m[1] as $dest) {
                        $queue->enqueue([ $current, $dest, $m[2] ]);
                    }
                }
                break;
            case '&':
                $m[2][$from] = $high;
                foreach ($m[1] as $dest) {
                    $queue->enqueue([ $current, $dest, in_array(false, $m[2], true) ]);
                }
                break;
        }
    }
    if (999 === $i) {
        $result1 = $c['low'] * $c['high'];
    }
}
echo 'Result 1: ', $result1, PHP_EOL;
echo 'Result 2: ', $result2, PHP_EOL;

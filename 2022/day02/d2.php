<?php

declare(strict_types=1);

enum Shape {
    case Rock;
    case Paper;
    case Scissors;

    public static function fromString(string $shape): self
    {
        return match ($shape) {
            'A', 'X' => self::Rock,
            'B', 'Y' => self::Paper,
            'C', 'Z' => self::Scissors,
        };
    }

    public function getPoints(): int
    {
        return match ($this) {
            self::Rock => 1,
            self::Paper => 2,
            self::Scissors => 3,
        };
    }

    public function compare(self $other): Result
    {
        if ($this === $other) {
            return Result::Draw;
        }

        return match ($this) {
            self::Rock => self::Scissors === $other ? Result::Win : Result::Lose,
            self::Paper => self::Rock === $other ? Result::Win : Result::Lose,
            self::Scissors => self::Paper === $other ? Result::Win : Result::Lose,
        };
    }
}

enum Result {
    case Lose;
    case Draw;
    case Win;

    public static function fromString(string $result): self
    {
        return match ($result) {
            'X' => self::Lose,
            'Y' => self::Draw,
            'Z' => self::Win,
        };
    }

    public function getPoints(): int
    {
        return match ($this) {
            self::Lose => 0,
            self::Draw => 3,
            self::Win => 6,
        };
    }
}

function part1(Shape $opponent, Shape $me): int
{
    return $me->compare($opponent)->getPoints() + $me->getPoints();
}

function part2(Shape $opponent, Result $result): int
{
    return (match ($result) {
        Result::Win => match ($opponent) {
            Shape::Rock => Shape::Paper,
            Shape::Paper => Shape::Scissors,
            Shape::Scissors => Shape::Rock,
        },
        Result::Draw => $opponent,
        Result::Lose => match ($opponent) {
            Shape::Rock => Shape::Scissors,
            Shape::Paper => Shape::Rock,
            Shape::Scissors => Shape::Paper,
        },
    })->getPoints() + $result->getPoints();
}

$score1 = $score2 = 0;
$rounds = explode(PHP_EOL, trim(file_get_contents('php://stdin')));

foreach ($rounds as $round) {
    [ $v1, $v2 ] = explode(' ', $round);

    $v1 = Shape::fromString($v1);
    $score1 += part1($v1, Shape::fromString($v2));
    $score2 += part2($v1, Result::fromString($v2));
}

printf("Score 1: %d\nScore 2: %d\n", $score1, $score2);

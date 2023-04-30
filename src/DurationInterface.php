<?php

declare(strict_types=1);

namespace Symandy\Component\Duration;

interface DurationInterface
{
    public const FORMAT_DEFAULT = 'default';
    public const FORMAT_SIMPLE = '%h:%m:%s';

    public function getDays(): int;

    public function setDays(int $days): self;

    public function addDays(int $days): self;

    public function subDays(int $days): self;

    public function getHours(): int;

    public function setHours(int $hours): self;

    public function addHours(int $hours): self;

    public function subHours(int $hours): self;

    public function getMinutes(): int;

    public function setMinutes(int $minutes): self;

    public function addMinutes(int $minutes): self;

    public function subMinutes(int $minutes): self;

    public function getSeconds(): int;

    public function setSeconds(int $seconds): self;

    public function addSeconds(int $seconds): self;

    public function subSeconds(int $seconds): self;

    public function create(string $duration): self;

    public function format(string $format = self::FORMAT_DEFAULT): ?string;
}

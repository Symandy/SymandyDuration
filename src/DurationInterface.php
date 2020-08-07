<?php

declare(strict_types=1);

namespace Symandy\Component\Duration;

interface DurationInterface
{
    public const FORMAT_DEFAULT = 'default';
    public const FORMAT_SIMPLE = '%h:%m:%s';

    /**
     * @return int|null
     */
    public function getDays(): ?int;

    /**
     * @param int|null $days
     * @return $this
     */
    public function setDays(?int $days): self;

    /**
     * @param int|null $days
     * @return $this
     */
    public function addDays(?int $days): self;

    /**
     * @param int|null $days
     * @return $this
     */
    public function subDays(?int $days): self;

    /**
     * @return int|null
     */
    public function getHours(): ?int;

    /**
     * @param int|null $hours
     * @return $this
     */
    public function setHours(?int $hours): self;

    /**
     * @param int|null $hours
     * @return $this
     */
    public function addHours(?int $hours): self;

    /**
     * @param int|null $hours
     * @return $this
     */
    public function subHours(?int $hours): self;

    /**
     * @return int|null
     */
    public function getMinutes(): ?int;

    /**
     * @param int|null $minutes
     * @return $this
     */
    public function setMinutes(?int $minutes): self;

    /**
     * @param int|null $minutes
     * @return $this
     */
    public function addMinutes(?int $minutes): self;

    /**
     * @param int|null $minutes
     * @return $this
     */
    public function subMinutes(?int $minutes): self;

    /**
     * @return int|null
     */
    public function getSeconds(): ?int;

    /**
     * @param int|null $seconds
     * @return $this
     */
    public function setSeconds(?int $seconds): self;

    /**
     * @param int|null $seconds
     * @return $this
     */
    public function addSeconds(?int $seconds): self;

    /**
     * @param int|null $seconds
     * @return $this
     */
    public function subSeconds(?int $seconds): self;

    /**
     * @param string|null $duration
     * @return static
     */
    public function create(?string $duration): self;

    /**
     * @param string $format
     * @return string|null
     */
    public function format(string $format = self::FORMAT_DEFAULT): ?string;
}

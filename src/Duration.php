<?php

declare(strict_types=1);

namespace Symandy\Component\Duration;

use InvalidArgumentException;

use function intdiv;
use function preg_match;
use function sprintf;

class Duration implements DurationInterface
{
    private const MAX_HOURS = 24;
    private const MAX_MINUTES = 60;
    private const MAX_SECONDS = 60;

    private const DAYS_REGEX = '/(?<value>[0-9]+)\s*[dD]/';
    private const HOURS_REGEX = '/(?<value>[0-9]+)\s*[hH]/';
    private const MINUTES_REGEX = '/(?<value>[0-9]+)\s*[mM]/';
    private const SECONDS_REGEX = '/(?<value>[0-9]+)\s*[sS]/';

    private int $days = 0;

    private int $hours = 0;

    private int $minutes = 0;

    private int $seconds = 0;

    public function __construct(?string $duration = null)
    {
        if (null !== $duration) {
            $this->create($duration);
        }
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function setDays(int $days): DurationInterface
    {
        $this->days = $days;

        return $this;
    }

    public function addDays(int $days): DurationInterface
    {
        return $this->setDays($this->days + $days);
    }

    public function subDays(int $days): DurationInterface
    {
        return $this->setDays($this->days - $days);
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function setHours(int $hours): DurationInterface
    {
        if (self::MAX_HOURS < $hours) {
            $this->addDays(intdiv($hours, self::MAX_HOURS));
            $this->hours = $hours % self::MAX_HOURS;

            return $this;
        }

        $this->hours = $hours;

        return $this;
    }

    public function addHours(int $hours): DurationInterface
    {
        return $this->setHours($this->hours + $hours);
    }

    public function subHours(int $hours): DurationInterface
    {
        return $this->setHours($this->hours - $hours);

    }

    public function getMinutes(): int
    {
        return $this->minutes;
    }

    public function setMinutes(int $minutes): DurationInterface
    {
        if (self::MAX_MINUTES < $minutes) {
            $this->addHours(intdiv($minutes, self::MAX_MINUTES));
            $this->minutes = $minutes % self::MAX_MINUTES;

            return $this;
        }

        $this->minutes = $minutes;

        return $this;
    }

    public function addMinutes(int $minutes): DurationInterface
    {
        return $this->setMinutes($this->minutes + $minutes);
    }

    public function subMinutes(int $minutes): DurationInterface
    {
        return $this->setMinutes($this->minutes - $minutes);
    }

    public function getSeconds(): int
    {
        return $this->seconds;
    }

    public function setSeconds(int $seconds): DurationInterface
    {
        if (self::MAX_SECONDS < $seconds) {
            $this->addMinutes(intdiv($seconds, self::MAX_SECONDS));
            $this->seconds = $seconds % self::MAX_SECONDS;

            return $this;
        }

        $this->seconds = $seconds;

        return $this;
    }

    public function addSeconds(int $seconds): DurationInterface
    {
        return $this->setSeconds($this->seconds + $seconds);
    }

    public function subSeconds(int $seconds): DurationInterface
    {
        return $this->setSeconds($this->seconds - $seconds);
    }

    public function create(string $duration): DurationInterface
    {
        $seconds = $this->parseRegex(self::SECONDS_REGEX, $duration, 'seconds');
        $this->addSeconds($seconds);

        $minutes = $this->parseRegex(self::MINUTES_REGEX, $duration, 'minutes');
        $this->addMinutes($minutes);

        $hours = $this->parseRegex(self::HOURS_REGEX, $duration, 'hours');
        $this->addHours($hours);

        $days = $this->parseRegex(self::DAYS_REGEX, $duration, 'days');
        $this->addDays($days);

        return $this;
    }

    public function format(string $format = self::FORMAT_DEFAULT): string
    {
        if (self::FORMAT_DEFAULT === $format) {
            return $this->formatDefault();
        }

        $duration = $format;

        $duration = preg_replace('/%d/', (string) $this->getDays(), $duration);

        if (null !== $duration) {
            $duration = preg_replace('/%h/', (string) $this->getHours(), $duration);
        }

        if (null !== $duration) {
            $duration = preg_replace('/%m/', (string) $this->getMinutes(), $duration);
        }

        if (null !== $duration) {
            $duration = preg_replace('/%s/', (string) $this->getSeconds(), $duration);
        }

        return $duration ?? '';
    }

    private function formatDefault(): string
    {
        $formattedDuration = '';
        $duration = array(
            'd' => $this->getDays(),
            'h' => $this->getHours(),
            'm' => $this->getMinutes(),
            's' => $this->getSeconds()
        );

        foreach ($duration as $unit => $value) {
            if (0 < $value) {
                $formattedDuration .= $value . $unit . ' ';
            }
        }

        return trim($formattedDuration);
    }

    private function parseRegex(string $regex, string $duration, string $type): int
    {
        $match = preg_match($regex, $duration, $matches);

        if (!$match || !isset($matches['value'])) {
            throw new InvalidArgumentException(sprintf(
                'Could not retrieve %s from duration "%s" with regex "%s"',
                $type,
                $duration,
                $regex
            ));
        }

        return (int) $matches['value'];
    }
}

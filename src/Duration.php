<?php

declare(strict_types=1);

namespace Symandy\Component\Duration;

class Duration implements DurationInterface
{
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
        $this->days += $days;

        return $this;
    }

    public function subDays(int $days): DurationInterface
    {
        $this->days -= $days;

        return $this;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function setHours(int $hours): DurationInterface
    {
        $this->hours = $hours;

        return $this;
    }

    public function addHours(int $hours): DurationInterface
    {
        $this->hours += $hours;

        return $this;
    }

    public function subHours(int $hours): DurationInterface
    {
        $this->hours -= $hours;

        return $this;
    }

    public function getMinutes(): int
    {
        return $this->minutes;
    }

    public function setMinutes(int $minutes): DurationInterface
    {
        $this->minutes = $minutes;

        return $this;
    }

    public function addMinutes(int $minutes): DurationInterface
    {
        $this->minutes += $minutes;

        return $this;
    }

    public function subMinutes(int $minutes): DurationInterface
    {
        $this->minutes -= $minutes;

        return $this;
    }

    public function getSeconds(): int
    {
        return $this->seconds;
    }

    public function setSeconds(int $seconds): DurationInterface
    {
        $this->seconds = $seconds;

        return $this;
    }

    public function addSeconds(int $seconds): DurationInterface
    {
        $this->seconds += $seconds;

        return $this;
    }

    public function subSeconds(int $seconds): DurationInterface
    {
        $this->seconds -= $seconds;

        return $this;
    }

    public function create(string $duration): DurationInterface
    {
        $this->parse($duration);

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

    private function parse(string $duration): void
    {
        $daysRegex = '/([0-9]+)\s*(?:d|D)/';
        $hoursRegex = '/([0-9]+)\s*(?:h|H)/';
        $minutesRegex = '/([0-9]+)\s*(?:m|M)/';
        $secondsRegex = '/([0-9]+)\s*(?:s|S)/';

        $this->parseRegex($secondsRegex, $duration, 'seconds');
        $this->parseRegex($minutesRegex, $duration, 'minutes');
        $this->parseRegex($hoursRegex, $duration, 'hours');
        $this->parseRegex($daysRegex, $duration, 'days');
    }

    private function parseRegex(string $regex, string $duration, string $type): void
    {
        if (preg_match($regex, $duration, $matches)) {
            $methodName = sprintf('add%s', ucfirst($type));

            if (method_exists($this, $methodName)) {
                $this->$methodName((int) $matches[1]);
                $this->calculate();
            }
        }
    }

    private function calculate(): void
    {
        $seconds = $this->getSeconds();
        $maxSeconds = 60;

        if (60 < $seconds) {
            $this->addMinutes(intdiv($seconds, $maxSeconds));
            $this->resetProperty('seconds', $seconds % $maxSeconds);
        }

        $minutes = $this->getMinutes();
        $maxMinutes = 60;

        if (60 < $minutes) {
            $this->addHours(intdiv($minutes, $maxMinutes));
            $this->resetProperty('minutes', $minutes % $maxMinutes);
        }

        $hours = $this->getHours();
        $maxHours = 24;

        if (24 < $hours) {
            $this->addDays(intdiv($hours, $maxHours));
            $this->resetProperty('hours', $hours % $maxHours);
        }
    }

    private function resetProperty(string $property, int $value): void
    {
        $setMethodName = sprintf('set%s', ucfirst($property));

        if (method_exists($this, $setMethodName)) {
            $this->$setMethodName(0);
        }

        $addMethodName = sprintf('add%s', ucfirst($property));

        if (method_exists($this ,$addMethodName)) {
            $this->$addMethodName($value);
        }
    }
}

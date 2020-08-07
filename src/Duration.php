<?php

declare(strict_types=1);

namespace Symandy\Component\Duration;

class Duration implements DurationInterface
{
    /** @var int|null */
    private ?int $days = 0;

    /** @var int|null */
    private ?int $hours = 0;

    /** @var int|null */
    private ?int $minutes = 0;

    /** @var int|null */
    private ?int $seconds = 0;

    public function __construct(?string $duration = null)
    {
        if (null !== $duration) {
            $this->create($duration);
        }
    }

    /**
     * @return int|null
     */
    public function getDays(): ?int
    {
        return $this->days;
    }

    /**
     * @param int|null $days
     * @return $this
     */
    public function setDays(?int $days): DurationInterface
    {
        $this->days = $days;

        return $this;
    }

    /**
     * @param int|null $days
     * @return $this
     */
    public function addDays(?int $days): DurationInterface
    {
        $this->days += $days;

        return $this;
    }

    /**
     * @param int|null $days
     * @return $this
     */
    public function subDays(?int $days): DurationInterface
    {
        $this->days -= $days;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHours(): ?int
    {
        return $this->hours;
    }

    /**
     * @param int|null $hours
     * @return $this
     */
    public function setHours(?int $hours): DurationInterface
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * @param int|null $hours
     * @return $this
     */
    public function addHours(?int $hours): DurationInterface
    {
        $this->hours += $hours;

        return $this;
    }

    /**
     * @param int|null $hours
     * @return $this
     */
    public function subHours(?int $hours): DurationInterface
    {
        $this->hours -= $hours;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinutes(): ?int
    {
        return $this->minutes;
    }

    /**
     * @param int|null $minutes
     * @return $this
     */
    public function setMinutes(?int $minutes): DurationInterface
    {
        $this->minutes = $minutes;

        return $this;
    }

    /**
     * @param int|null $minutes
     * @return $this
     */
    public function addMinutes(?int $minutes): DurationInterface
    {
        $this->minutes += $minutes;

        return $this;
    }

    /**
     * @param int|null $minutes
     * @return $this
     */
    public function subMinutes(?int $minutes): DurationInterface
    {
        $this->minutes -= $minutes;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSeconds(): ?int
    {
        return $this->seconds;
    }

    /**
     * @param int|null $seconds
     * @return $this
     */
    public function setSeconds(?int $seconds): DurationInterface
    {
        $this->seconds = $seconds;

        return $this;
    }

    /**
     * @param int|null $seconds
     * @return $this
     */
    public function addSeconds(?int $seconds): DurationInterface
    {
        $this->seconds += $seconds;

        return $this;
    }

    /**
     * @param int|null $seconds
     * @return $this
     */
    public function subSeconds(?int $seconds): DurationInterface
    {
        $this->seconds -= $seconds;

        return $this;
    }

    /**
     * @param string|null $duration
     * @return static
     */
    public function create(?string $duration): DurationInterface
    {
        $this->parse($duration);

        return $this;
    }

    /**
     * @param string $format
     * @return string|null
     */
    public function format(string $format = self::FORMAT_DEFAULT): ?string
    {
        if (self::FORMAT_DEFAULT === $format) {
            return $this->formatDefault();
        }

        $duration = $format;

        $duration = preg_replace('/%d/', (string) $this->getDays(), $duration);
        $duration = preg_replace('/%h/', (string) $this->getHours(), $duration);
        $duration = preg_replace('/%m/', (string) $this->getMinutes(), $duration);
        $duration = preg_replace('/%s/', (string) $this->getSeconds(), $duration);

        return $duration;
    }

    /**
     * @return string
     */
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

    /**
     * @param string|null $duration
     */
    private function parse(?string $duration): void
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

    /**
     * @param string|null $regex
     * @param string|null $duration
     * @param string|null $type
     */
    private function parseRegex(?string $regex, ?string $duration, ?string $type): void
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
            $this->resetType('seconds', $seconds % $maxSeconds);
        }

        $minutes = $this->getMinutes();
        $maxMinutes = 60;

        if (60 < $minutes) {
            $this->addHours(intdiv($minutes, $maxMinutes));
            $this->resetType('minutes', $minutes % $maxMinutes);
        }

        $hours = $this->getHours();
        $maxHours = 24;

        if (24 < $hours) {
            $this->addDays(intdiv($hours, $maxHours));
            $this->resetType('hours', $hours % $maxHours);
        }
    }

    /**
     * @param string|null $type
     * @param int|null $value
     */
    private function resetType(?string $type, ?int $value): void
    {
        $setMethodName = sprintf('set%s', ucfirst($type));

        if (method_exists($this, $setMethodName)) {
            $this->$setMethodName(0);
        }

        $addMethodName = sprintf('add%s', ucfirst($type));

        if (method_exists($this ,$addMethodName)) {
            $this->$addMethodName($value);
        }
    }
}

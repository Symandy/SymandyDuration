<?php

declare(strict_types=1);

namespace Symandy\Component\Duration\Test;

use PHPUnit\Framework\TestCase;
use Symandy\Component\Duration\Duration;
use Symandy\Component\Duration\DurationInterface;

class DurationTest extends TestCase
{
    public function testDurationCreation(): void
    {
        $goodSamples = array(
            array(0, 0, 0, 0),
            array(0, 0, 0, 5),
            array(0, 0, 0, 10),
            array(0, 0, 5, 0),
            array(0, 0, 10, 0),
            array(0, 5, 0, 0),
            array(0, 10, 0, 0),
            array(5, 0, 0, 0),
            array(10, 0, 0, 0),
            array(10, 10, 10, 10),
            array(0, 19, 59, 00)
        );

        $badSamples = array(
            array(0, 0, 0, 70),
            array(0, 0, 0, 150),
            array(0, 0, 70, 0),
            array(0, 0, 150, 0),
            array(0, 0, 150, 70),
            array(0, 90, 0, 0),
            array(0, 90, 150, 0),
            array(0, 90, 150, 150),
            array(0, 0, 0, 3600),
            array(0, 24, 3600, 3600),
            array(40, 400, 40000, 40000)
        );

        foreach (array_merge($goodSamples, $badSamples) as $sample) {
            $this->assertDurationIsCreated($sample[0], $sample[1], $sample[2], $sample[3]);
        }
    }

    public function testDurationFormat(): void
    {
        $samples = array(
            array(0, 0, 0, 0),
            array(0, 0, 4, 0),
            array(0, 4, 0, 0),
            array(4, 0, 0, 0),
        );

        $formats = array(
            1 => DurationInterface::FORMAT_DEFAULT,
            2 => DurationInterface::FORMAT_SIMPLE,
            3 => '%dd %hh %mm %ss',
            4 => '%d days %h hours %m minutes %s seconds'
        );

        foreach ($samples as $sample) {
            $duration = $this->initializeDurationByValues($sample[0], $sample[1], $sample[2], $sample[3]);

            foreach ($formats as $key => $format) {
                $formattedDuration = $duration->format($format);

                switch ($key) {
                    default:
                    case 1:
                        $expectedDuration = '';
                        $data = array(
                            'd' => $duration->getDays(),
                            'h' => $duration->getHours(),
                            'm' => $duration->getMinutes(),
                            's' => $duration->getSeconds()
                        );

                        foreach ($data as $unit => $value) {
                            if (0 < $value) {
                                $expectedDuration .= $value . $unit . ' ';
                            }
                        }

                        $expectedDuration = trim($expectedDuration);
                        break;

                    case 2:
                        $expectedDuration = trim(
                            $duration->getHours() . ':' .
                            $duration->getMinutes() . ':' .
                            $duration->getSeconds()
                        );
                        break;

                    case 3:
                        $expectedDuration = trim(
                            $duration->getDays() . 'd ' .
                            $duration->getHours() . 'h ' .
                            $duration->getMinutes() . 'm ' .
                            $duration->getSeconds() . 's'
                        );
                        break;

                    case 4:
                        $expectedDuration = trim(
                            $duration->getDays() . ' days ' .
                            $duration->getHours() . ' hours ' .
                            $duration->getMinutes() . ' minutes ' .
                            $duration->getSeconds() . ' seconds'
                        );
                        break;
                }

                self::assertEquals($expectedDuration, $formattedDuration);
            }
        }
    }

    /**
     * @param string|null $duration
     * @return DurationInterface
     */
    private function initializeDuration(?string $duration = null): DurationInterface
    {
        return new Duration($duration);
    }

    /**
     * @param int|null $days
     * @param int|null $hours
     * @param int|null $minutes
     * @param int|null $seconds
     * @return DurationInterface
     */
    private function initializeDurationByValues(?int $days, ?int $hours, ?int $minutes, ?int $seconds): DurationInterface
    {
        $daysSample = array('days', 'd', 'Days', 'daYs', 'DAYS');
        $hoursSample = array('hours', 'h', 'Hours', 'HOURS', 'hOURS');
        $minutesSample = array('minutes', 'm', 'Minutes', 'MINUTES');
        $secondsSample = array('seconds', 's', 'Seconds', 'SECONDS');

        $regex = $this->addToRegex(null, $days, $daysSample);
        $regex = $this->addToRegex($regex, $hours, $hoursSample);
        $regex = $this->addToRegex($regex, $minutes, $minutesSample);
        $regex = $this->addToRegex($regex, $seconds, $secondsSample);

        return $this->initializeDuration($regex);
    }

    /**
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     */
    private function assertDurationIsCreated(int $days, int $hours, int $minutes, int $seconds): void
    {
        $duration = $this->initializeDurationByValues($days, $hours, $minutes, $seconds);

        $expectedDuration = array(
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds
        );

        $expectedDuration = $this->calculateExpectedDuration($expectedDuration);

        self::assertEquals($expectedDuration['days'], $duration->getDays());
        self::assertEquals($expectedDuration['hours'], $duration->getHours());
        self::assertEquals($expectedDuration['minutes'], $duration->getMinutes());
        self::assertEquals($expectedDuration['seconds'], $duration->getSeconds());
    }

    /**
     * @param array<string, int|null> $duration
     * @return array<string, int|null>
     */
    private function calculateExpectedDuration(array $duration): array
    {
        $seconds = $duration['seconds'];
        $maxSeconds = 60;

        if ($maxSeconds < $seconds) {
            $duration['minutes'] += intdiv($seconds, $maxSeconds);
            $duration['seconds'] = $seconds % $maxSeconds;
        }

        $minutes = $duration['minutes'];
        $maxMinutes = 60;

        if ($maxMinutes < $minutes) {
            $duration['hours'] += intdiv($minutes, $maxMinutes);
            $duration['minutes'] = $minutes % $maxMinutes;
        }

        $hours = $duration['hours'];
        $maxHours = 24;

        if ($maxHours < $hours) {
            $duration['days'] += intdiv($hours, $maxHours);
            $duration['hours'] = $hours % $maxHours;
        }

        return $duration;
    }

    /**
     * @param string|null $regex
     * @param int|null $number
     * @param array<string> $wordsSample
     * @return string
     */
    private function addToRegex(?string $regex, ?int $number, array $wordsSample): string
    {
        $regex = null === $regex ? '' : $regex . ' ';

        if (null !== $number) {
            $regex .= $number . ' ' . $wordsSample[rand(0, count($wordsSample) - 1)];
        }

        return trim($regex);
    }
}

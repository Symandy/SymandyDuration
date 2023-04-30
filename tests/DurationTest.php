<?php

declare(strict_types=1);

namespace Symandy\Component\Duration\Test;

use PHPUnit\Framework\TestCase;
use Symandy\Component\Duration\Duration;
use Symandy\Component\Duration\DurationInterface;

use function count;
use function random_int;

/**
 * @covers \Symandy\Component\Duration\Duration
 */
final class DurationTest extends TestCase
{
    /**
     * @dataProvider provideCreateCases
     */
    public function testCreate(DurationInterface $expected, int $days, int $hours, int $minutes, int $seconds): void
    {
        $duration = $this->createDuration($days, $hours, $minutes, $seconds);

        self::assertEquals($expected, $duration);
    }

    /**
     * @dataProvider provideFormatCases
     */
    public function testFormat(string $expected, string $format, int $days, int $hours, int $minutes, int $seconds): void
    {
        $duration = $this->createDuration($days, $hours, $minutes, $seconds);

        self::assertSame($expected, $duration->format($format));
    }

    /**
     * @return iterable<string, array{0: DurationInterface, 1: int, 2: int, 3: int, 4: int}>
     */
    public static function provideCreateCases(): iterable
    {
        // Normal cases
        $duration = new Duration('0 days 0 hours 0 minutes 0 seconds');
        yield '0_0_0_0' => [$duration, 0, 0, 0, 0];

        $duration = new Duration('0 days 0 hours 0 minutes 5 seconds');
        yield '0_0_0_5' => [$duration, 0, 0, 0, 5];

        $duration = new Duration('0 days 0 hours 0 minutes 10 seconds');
        yield '0_0_0_10' => [$duration, 0, 0, 0, 10];

        $duration = new Duration('0 days 0 hours 5 minutes 0 seconds');
        yield '0_0_5_0' => [$duration, 0, 0, 5, 0];

        $duration = new Duration('0 days 0 hours 10 minutes 0 seconds');
        yield '0_0_10_0' => [$duration, 0, 0, 10, 0];

        $duration = new Duration('0 days 5 hours 0 minutes 0 seconds');
        yield '0_5_0_0' => [$duration, 0, 5, 0, 0];

        $duration = new Duration('0 days 10 hours 0 minutes 0 seconds');
        yield '0_10_0_0' => [$duration, 0, 10, 0, 0];

        $duration = new Duration('5 days 0 hours 0 minutes 0 seconds');
        yield '5_0_0_0' => [$duration, 5, 0, 0, 0];

        $duration = new Duration('10 days 0 hours 0 minutes 0 seconds');
        yield '10_0_0_0' => [$duration, 10, 0, 0, 0];

        $duration = new Duration('10 days 10 hours 10 minutes 10 seconds');
        yield '10_10_10_10' => [$duration, 10, 10, 10, 10];

        $duration = new Duration('0 days 19 hours 59 minutes 0 seconds');
        yield '0_19_59_0' => [$duration, 0, 19, 59, 0];

        // Wrong format cases
        $duration = new Duration('0 days 0 hours 1 minute 10 seconds');
        yield '0_0_0_70' => [$duration, 0, 0, 0, 70];

        $duration = new Duration('0 days 0 hours 2 minutes 30 seconds');
        yield '0_0_0_150' => [$duration, 0, 0, 0, 150];

        $duration = new Duration('0 days 1 hours 10 minute 0 seconds');
        yield '0_0_70_0' => [$duration, 0, 0, 70, 0];

        $duration = new Duration('0 days 2 hours 30 minutes 0 seconds');
        yield '0_0_150_0' => [$duration, 0, 0, 150, 0];

        $duration = new Duration('0 days 2 hours 31 minutes 10 seconds');
        yield '0_0_150_70' => [$duration, 0, 0, 150, 70];

        $duration = new Duration('3 days 18 hours 0 minutes 0 seconds');
        yield '0_90_0_0' => [$duration, 0, 90, 0, 0];

        $duration = new Duration('3 days 20 hours 30 minutes 00 seconds');
        yield '0_90_150_0' => [$duration, 0, 90, 150, 0];

        $duration = new Duration('3 days 20 hours 32 minutes 30 seconds');
        yield '0_90_150_150' => [$duration, 0, 90, 150, 150];

        $duration = new Duration('0 days 0 hours 60 minutes 0 seconds');
        yield '0_0_0_3600' => [$duration, 0, 0, 0, 3600];

        $duration = new Duration('3 days 13 hours 0 minutes 0 seconds');
        yield '0_24_3600_3600' => [$duration, 0, 24, 3600, 3600];

        $duration = new Duration('84 days 21 hours 46 minutes 40 seconds');
        yield '40_400_40000_40000' => [$duration, 40, 400, 40000, 40000];
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2: int, 3: int, 4: int, 5: int}>
     */
    public static function provideFormatCases(): iterable
    {
        yield 'default_0_0_0_0' => ['', DurationInterface::FORMAT_DEFAULT, 0, 0, 0, 0];
        yield 'simple_0_0_0_0' => ['0:0:0', DurationInterface::FORMAT_SIMPLE, 0, 0, 0, 0];
        yield '0d_0h_0m_0s' => ['0d 0h 0m 0s', '%dd %hh %mm %ss', 0, 0, 0, 0];
        yield '0days_0hours_0minutes_0seconds' => [
            '0 days 0 hours 0 minutes 0 seconds',
            '%d days %h hours %m minutes %s seconds',
            0, 0, 0, 0
        ];

        yield 'default_0_0_0_4' => ['4s', DurationInterface::FORMAT_DEFAULT, 0, 0, 0, 4];
        yield 'simple_0_0_0_4' => ['0:0:4', DurationInterface::FORMAT_SIMPLE, 0, 0, 0, 4];
        yield '0d_0h_0m_4s' => ['0d 0h 0m 4s', '%dd %hh %mm %ss', 0, 0, 0, 4];
        yield '0days_0hours_0minutes_4seconds' => [
            '0 days 0 hours 0 minutes 4 seconds',
            '%d days %h hours %m minutes %s seconds',
            0, 0, 0, 4
        ];

        yield 'default_0_0_4_0' => ['4m', DurationInterface::FORMAT_DEFAULT, 0, 0, 4, 0];
        yield 'simple_0_0_4_0' => ['0:4:0', DurationInterface::FORMAT_SIMPLE, 0, 0, 4, 0];
        yield '0d_0h_4m_0s' => ['0d 0h 4m 0s', '%dd %hh %mm %ss', 0, 0, 4, 0];
        yield '0days_0hours_4minutes_0seconds' => [
            '0 days 0 hours 4 minutes 0 seconds',
            '%d days %h hours %m minutes %s seconds',
            0, 0, 4, 0
        ];

        yield 'default_0_4_0_0' => ['4h', DurationInterface::FORMAT_DEFAULT, 0, 4, 0, 0];
        yield 'simple_0_4_0_0' => ['4:0:0', DurationInterface::FORMAT_SIMPLE, 0, 4, 0, 0];
        yield '0d_4h_0m_0s' => ['0d 4h 0m 0s', '%dd %hh %mm %ss', 0, 4, 0, 0];
        yield '0days_4hours_0minutes_0seconds' => [
            '0 days 4 hours 0 minutes 0 seconds',
            '%d days %h hours %m minutes %s seconds',
            0, 4, 0, 0
        ];

        yield 'default_4_0_0_0' => ['4d', DurationInterface::FORMAT_DEFAULT, 4, 0, 0, 0];
        yield 'simple_4_0_0_0' => ['0:0:0', DurationInterface::FORMAT_SIMPLE, 4, 0, 0, 0];
        yield '4d_0h_0m_0s' => ['4d 0h 0m 0s', '%dd %hh %mm %ss', 4, 0, 0, 0];
        yield '4days_0hours_0minutes_0seconds' => [
            '4 days 0 hours 0 minutes 0 seconds',
            '%d days %h hours %m minutes %s seconds',
            4, 0, 0, 0
        ];
    }

    private function createDuration(int $days, int $hours, int $minutes, int $seconds): DurationInterface
    {
        $daysSample = ['days', 'd', 'Days', 'daYs', 'DAYS'];
        $hoursSample = ['hours', 'h', 'Hours', 'HOURS', 'hOURS'];
        $minutesSample = ['minutes', 'm', 'Minutes', 'MINUTES'];
        $secondsSample = ['seconds', 's', 'Seconds', 'SECONDS'];

        return new Duration(
            $days . ' ' . $daysSample[random_int(0, count($daysSample) - 1)] .
            $hours . ' ' . $hoursSample[random_int(0, count($hoursSample) - 1)] .
            $minutes . ' ' . $minutesSample[random_int(0, count($minutesSample) - 1)] .
            $seconds . ' ' . $secondsSample[random_int(0, count($secondsSample) - 1)]
        );
    }
}

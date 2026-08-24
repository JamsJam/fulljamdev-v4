<?php

namespace App\Tests\UI\Unit\DatePicker;

use App\UI\DatePicker\Builder\DatePickerMonthBuilder;
use App\UI\DatePicker\Resolver\DatePickerMonthResolver;
use App\UI\DatePicker\Service\DatePickerService;
use PHPUnit\Framework\TestCase;

final class DatePickerServiceTest extends TestCase
{
    private DatePickerService $datePicker;

    protected function setUp(): void
    {
        $this->datePicker = new DatePickerService(
            new DatePickerMonthResolver(),
            new DatePickerMonthBuilder(),
        );
    }

    public function testItIsTheFacadeForResolvingAndBuildingAMonth(): void
    {
        $month = $this->datePicker->resolveMonth('2026-08');
        $calendar = $this->datePicker->create($month, ['2026-08-19']);

        self::assertSame('2026-08-01', $month->format('Y-m-d'));
        self::assertSame('2026-08', $calendar->key);
        self::assertCount(6, $calendar->weeks);
    }

    public function testItRejectsAnInvalidMonth(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->datePicker->resolveMonth('août 2026');
    }
}

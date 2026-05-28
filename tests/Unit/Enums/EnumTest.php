<?php

namespace Tests\Unit\Enums;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskRecurEnum;
use App\Enums\WeekdayEnum;
use Tests\TestCase;

class EnumTest extends TestCase
{
    public function test_priority_enum_ordered(): void
    {
        $this->assertEquals(['high', 'medium', 'low', 'none'], TaskPriorityEnum::ordered());
    }

    public function test_recur_enum_values(): void
    {
        $this->assertEquals(['daily', 'weekdays', 'weekends', 'weekly'], TaskRecurEnum::values());
    }

    public function test_weekday_enum_values(): void
    {
        $this->assertEquals(
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            WeekdayEnum::values()
        );
    }
}

<?php

namespace Tests\Unit\Models;

use App\Enums\TaskRecurEnum;
use App\Enums\WeekdayEnum;
use App\Models\RecurringTaskTemplate;
use App\Models\RecurringTaskTemplatePeriod;
use App\Models\RecurringTaskTemplateWeekday;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringTaskTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-05-06 12:00:00', 'Europe/Skopje'));
    }

    public function test_daily_is_due_every_day(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['recur' => TaskRecurEnum::DAILY]);

        $this->assertTrue($template->isDueOnDate(Carbon::parse('2026-05-05')));
        $this->assertTrue($template->isDueOnDate(Carbon::parse('2026-05-10')));
        $this->assertTrue($template->isDueOnDate(Carbon::parse('2026-05-11'))); // Sunday
    }

    public function test_weekdays_is_due_on_monday(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['recur' => TaskRecurEnum::WEEKDAYS]);

        $this->assertTrue($template->isDueOnDate(Carbon::parse('2026-05-04'))); // Monday
    }

    public function test_weekdays_is_not_due_on_saturday(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['recur' => TaskRecurEnum::WEEKDAYS]);

        $this->assertFalse($template->isDueOnDate(Carbon::parse('2026-05-09'))); // Saturday
    }

    public function test_weekdays_is_not_due_on_sunday(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['recur' => TaskRecurEnum::WEEKDAYS]);

        $this->assertFalse($template->isDueOnDate(Carbon::parse('2026-05-10'))); // Sunday
    }

    public function test_weekends_is_due_on_saturday(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['recur' => TaskRecurEnum::WEEKENDS]);

        $this->assertTrue($template->isDueOnDate(Carbon::parse('2026-05-09'))); // Saturday
    }

    public function test_weekends_is_due_on_sunday(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['recur' => TaskRecurEnum::WEEKENDS]);

        $this->assertTrue($template->isDueOnDate(Carbon::parse('2026-05-10'))); // Sunday
    }

    public function test_weekends_is_not_due_on_monday(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['recur' => TaskRecurEnum::WEEKENDS]);

        $this->assertFalse($template->isDueOnDate(Carbon::parse('2026-05-04'))); // Monday
    }

    public function test_weekly_is_due_on_matching_weekday(): void
    {
        $template = RecurringTaskTemplate::factory()->weekly()->create();
        RecurringTaskTemplateWeekday::create([
            'recurring_task_template_id' => $template->id,
            'weekday' => WeekdayEnum::MONDAY,
        ]);
        $template->load('weekdays');

        $this->assertTrue($template->isDueOnDate(Carbon::parse('2026-05-04'))); // Monday
    }

    public function test_weekly_is_not_due_on_non_matching_weekday(): void
    {
        $template = RecurringTaskTemplate::factory()->weekly()->create();
        RecurringTaskTemplateWeekday::create([
            'recurring_task_template_id' => $template->id,
            'weekday' => WeekdayEnum::MONDAY,
        ]);
        $template->load('weekdays');

        $this->assertFalse($template->isDueOnDate(Carbon::parse('2026-05-05'))); // Tuesday
    }

    public function test_is_active_attribute_true_when_open_period_exists(): void
    {
        $template = RecurringTaskTemplate::factory()->create();

        $this->assertTrue($template->is_active);
    }

    public function test_is_active_attribute_false_when_all_periods_closed(): void
    {
        $template = RecurringTaskTemplate::factory()->create();
        RecurringTaskTemplatePeriod::where('recurring_task_template_id', $template->id)
            ->update(['end_date' => now()->toDateString()]);

        $this->assertFalse($template->is_active);
    }

    public function test_is_active_attribute_false_when_no_periods_exist(): void
    {
        $template = RecurringTaskTemplate::factory()->create();
        RecurringTaskTemplatePeriod::where('recurring_task_template_id', $template->id)->delete();

        $this->assertFalse($template->is_active);
    }

    public function test_to_virtual_task_returns_unsaved_task_with_correct_fields(): void
    {
        $template = RecurringTaskTemplate::factory()->create(['title' => 'Daily workout']);
        $template->load('labels');
        $date = Carbon::parse('2026-05-10');

        $task = $template->toVirtualTask($date);

        $this->assertNull($task->id);
        $this->assertTrue($task->is_virtual);
        $this->assertEquals('Daily workout', $task->title);
        $this->assertEquals($template->user_id, $task->user_id);
        $this->assertEquals($template->id, $task->recurring_task_template_id);
        $this->assertEquals('2026-05-10', $task->date->toDateString());
    }
}

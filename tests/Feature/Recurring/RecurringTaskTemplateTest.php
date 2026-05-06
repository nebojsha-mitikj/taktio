<?php

namespace Tests\Feature\Recurring;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskRecurEnum;
use App\Enums\WeekdayEnum;
use App\Models\Label;
use App\Models\RecurringTaskTemplate;
use App\Models\RecurringTaskTemplatePeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class RecurringTaskTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-05-06 12:00:00', 'Europe/Skopje'));
    }

    private function user(): User
    {
        return User::factory()->withoutTwoFactor()->create();
    }

    private function dailyPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Daily standup',
            'description' => null,
            'priority' => TaskPriorityEnum::NONE->value,
            'recur' => TaskRecurEnum::DAILY->value,
            'label_ids' => [],
        ], $overrides);
    }

    // Recurring page

    public function test_recurring_page_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user())->get(route('recurring.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('recurring/Recurring')
        );
    }

    public function test_recurring_page_redirects_guests(): void
    {
        $this->get(route('recurring.index'))->assertRedirect(route('login'));
    }

    public function test_recurring_page_shows_only_current_users_templates(): void
    {
        $user = $this->user();
        $mine = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);
        RecurringTaskTemplate::factory()->create();

        $response = $this->actingAs($user)->get(route('recurring.index'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('recurring/Recurring')
            ->has('templates', 1)
            ->where('templates.0.id', $mine->id)
        );
    }

    // Store

    public function test_store_creates_daily_template(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->post(route('recurring.store'), $this->dailyPayload());

        $response->assertRedirect();
        $template = RecurringTaskTemplate::where('user_id', $user->id)->first();
        $this->assertNotNull($template);
        $this->assertEquals(TaskRecurEnum::DAILY, $template->recur);
        $this->assertDatabaseHas('recurring_task_template_periods', [
            'recurring_task_template_id' => $template->id,
            'end_date' => null,
        ]);
    }

    public function test_store_redirects_guests(): void
    {
        $this->post(route('recurring.store'), $this->dailyPayload())->assertRedirect(route('login'));
    }

    public function test_store_creates_weekly_template_with_weekdays(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->post(route('recurring.store'), [
            'title' => 'Weekly review',
            'description' => null,
            'priority' => TaskPriorityEnum::NONE->value,
            'recur' => TaskRecurEnum::WEEKLY->value,
            'weekdays' => [WeekdayEnum::MONDAY->value, WeekdayEnum::FRIDAY->value],
            'label_ids' => [],
        ]);

        $response->assertRedirect();
        $template = RecurringTaskTemplate::where('user_id', $user->id)->first();
        $this->assertEqualsCanonicalizing(
            [WeekdayEnum::MONDAY, WeekdayEnum::FRIDAY],
            $template->weekdays->pluck('weekday')->all()
        );
    }

    public function test_store_requires_title(): void
    {
        $response = $this->actingAs($this->user())->post(route('recurring.store'), [
            'priority' => TaskPriorityEnum::NONE->value,
            'recur' => TaskRecurEnum::DAILY->value,
            'label_ids' => [],
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_store_requires_weekdays_for_weekly_recurrence(): void
    {
        $response = $this->actingAs($this->user())->post(route('recurring.store'), [
            'title' => 'Missing weekdays',
            'priority' => TaskPriorityEnum::NONE->value,
            'recur' => TaskRecurEnum::WEEKLY->value,
            'label_ids' => [],
        ]);

        $response->assertSessionHasErrors('weekdays');
    }

    public function test_store_prohibits_weekdays_for_non_weekly_recurrence(): void
    {
        $response = $this->actingAs($this->user())->post(route('recurring.store'), [
            'title' => 'Bad weekdays',
            'priority' => TaskPriorityEnum::NONE->value,
            'recur' => TaskRecurEnum::DAILY->value,
            'weekdays' => [WeekdayEnum::MONDAY->value],
            'label_ids' => [],
        ]);

        $response->assertSessionHasErrors('weekdays');
    }

    // Update

    public function test_update_changes_template_fields(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id, 'title' => 'Old']);

        $response = $this->actingAs($user)->put(route('recurring.update', $template), [
            'title' => 'New title',
            'priority' => TaskPriorityEnum::HIGH->value,
            'recur' => TaskRecurEnum::WEEKDAYS->value,
            'label_ids' => [],
        ]);

        $response->assertRedirect();
        $fresh = $template->fresh();
        $this->assertEquals('New title', $fresh->title);
        $this->assertEquals(TaskPriorityEnum::HIGH, $fresh->priority);
        $this->assertEquals(TaskRecurEnum::WEEKDAYS, $fresh->recur);
    }

    public function test_update_forbidden_for_other_users_template(): void
    {
        $template = RecurringTaskTemplate::factory()->create();

        $response = $this->actingAs($this->user())->put(
            route('recurring.update', $template),
            $this->dailyPayload()
        );

        $response->assertForbidden();
    }

    // Destroy

    public function test_destroy_deletes_template(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('recurring.destroy', $template));

        $response->assertRedirect();
        $this->assertDatabaseMissing('recurring_task_templates', ['id' => $template->id]);
    }

    public function test_destroy_forbidden_for_other_users_template(): void
    {
        $template = RecurringTaskTemplate::factory()->create();

        $response = $this->actingAs($this->user())->delete(route('recurring.destroy', $template));

        $response->assertForbidden();
    }

    // Toggle

    public function test_toggle_deactivates_active_template_by_setting_end_date(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);
        RecurringTaskTemplatePeriod::where('recurring_task_template_id', $template->id)
            ->update(['start_date' => now()->subDay()->toDateString()]);

        $this->actingAs($user)->put(route('recurring.toggle', $template));

        $period = RecurringTaskTemplatePeriod::where('recurring_task_template_id', $template->id)->first();
        $this->assertNotNull($period->end_date);
        $this->assertEquals(now()->toDateString(), $period->end_date->toDateString());
    }

    public function test_toggle_deletes_period_started_today_when_deactivating(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('recurring.toggle', $template));

        $this->assertDatabaseMissing('recurring_task_template_periods', [
            'recurring_task_template_id' => $template->id,
        ]);
    }

    public function test_toggle_activates_inactive_template_by_creating_new_period(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);
        // Backdate so toggle can create a new period starting today without hitting
        // the unique (start_date, template_id) constraint.
        RecurringTaskTemplatePeriod::where('recurring_task_template_id', $template->id)
            ->update([
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->subDay()->toDateString(),
            ]);

        $this->actingAs($user)->put(route('recurring.toggle', $template));

        $newPeriod = RecurringTaskTemplatePeriod::where('recurring_task_template_id', $template->id)
            ->whereNull('end_date')
            ->first();

        $this->assertNotNull($newPeriod);
        $this->assertEquals(now()->toDateString(), $newPeriod->start_date->toDateString());
    }

    // Update labels

    public function test_update_labels_syncs_labels_on_template(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);
        $label = Label::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('recurring.labels.update', $template), [
            'label_ids' => [$label->id],
        ]);

        $response->assertRedirect();
        $this->assertTrue($template->fresh()->labels->contains($label));
    }

    public function test_update_labels_forbidden_for_other_users_template(): void
    {
        $template = RecurringTaskTemplate::factory()->create();

        $response = $this->actingAs($this->user())->put(route('recurring.labels.update', $template), [
            'label_ids' => [],
        ]);

        $response->assertForbidden();
    }
}

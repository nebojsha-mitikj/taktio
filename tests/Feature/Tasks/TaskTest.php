<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Label;
use App\Models\RecurringTaskTemplate;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TaskTest extends TestCase
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

    // Today

    public function test_today_page_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user())->get(route('tasks.today'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/Today')
        );
    }

    public function test_today_page_redirects_guests(): void
    {
        $this->get(route('tasks.today'))->assertRedirect(route('login'));
    }

    public function test_today_page_includes_tasks_for_today(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('tasks.today'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/Today')
            ->has('tasks', 1)
            ->where('tasks.0.id', $task->id)
        );
    }

    public function test_today_page_excludes_other_users_tasks(): void
    {
        $user = $this->user();
        Task::factory()->dueToday()->create();

        $response = $this->actingAs($user)->get(route('tasks.today'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/Today')
            ->has('tasks', 0)
        );
    }

    public function test_today_page_includes_virtual_task_from_active_daily_recurring_template(): void
    {
        $user = $this->user();
        RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('tasks.today'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/Today')
            ->has('tasks', 1)
            ->where('tasks.0.is_virtual', true)
        );
    }

    public function test_today_page_does_not_duplicate_materialized_recurring_task(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->dueToday()->create([
            'user_id' => $user->id,
            'recurring_task_template_id' => $template->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.today'));

        // The template must not generate a duplicate virtual entry alongside the real task.
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/Today')
            ->has('tasks', 1)
            ->where('tasks.0.id', $task->id)
        );
    }

    // Upcoming

    public function test_upcoming_page_renders(): void
    {
        $response = $this->actingAs($this->user())->get(route('tasks.upcoming'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/Upcoming')
        );
    }

    public function test_upcoming_page_redirects_guests(): void
    {
        $this->get(route('tasks.upcoming'))->assertRedirect(route('login'));
    }

    public function test_upcoming_page_groups_tasks_by_date(): void
    {
        $user = $this->user();
        $tomorrow = now()->addDay()->toDateString();
        Task::factory()->create(['user_id' => $user->id, 'date' => $tomorrow]);
        Task::factory()->create(['user_id' => $user->id, 'date' => $tomorrow]);

        $response = $this->actingAs($user)->get(route('tasks.upcoming'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/Upcoming')
            ->has('tasksByDate', 1)
            ->has("tasksByDate.{$tomorrow}", 2)
        );
    }

    // History

    public function test_history_page_renders(): void
    {
        $response = $this->actingAs($this->user())->get(route('tasks.history'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/History')
        );
    }

    public function test_history_page_redirects_guests(): void
    {
        $this->get(route('tasks.history'))->assertRedirect(route('login'));
    }

    public function test_history_page_shows_past_tasks_and_excludes_today(): void
    {
        $user = $this->user();
        $yesterday = now()->subDay()->toDateString();
        Task::factory()->create(['user_id' => $user->id, 'date' => $yesterday]);
        Task::factory()->dueToday()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('tasks.history'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('tasks/History')
            ->has('tasksByDate', 1)
            ->has("tasksByDate.{$yesterday}", 1)
        );
    }

    // Store

    public function test_store_creates_task(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Write tests',
            'date' => now()->toDateString(),
            'priority' => TaskPriorityEnum::MEDIUM->value,
            'label_ids' => [],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Write tests',
            'priority' => TaskPriorityEnum::MEDIUM->value,
            'status' => TaskStatusEnum::TO_DO->value,
        ]);
    }

    public function test_store_redirects_guests(): void
    {
        $this->post(route('tasks.store'), [])->assertRedirect(route('login'));
    }

    public function test_store_requires_title(): void
    {
        $response = $this->actingAs($this->user())->post(route('tasks.store'), [
            'date' => now()->toDateString(),
            'priority' => TaskPriorityEnum::NONE->value,
            'label_ids' => [],
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_store_rejects_past_date(): void
    {
        $response = $this->actingAs($this->user())->post(route('tasks.store'), [
            'title' => 'Old task',
            'date' => now()->subDay()->toDateString(),
            'priority' => TaskPriorityEnum::NONE->value,
            'label_ids' => [],
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_store_rejects_label_from_another_user(): void
    {
        $foreignLabel = Label::factory()->create();

        $response = $this->actingAs($this->user())->post(route('tasks.store'), [
            'title' => 'Task',
            'date' => now()->toDateString(),
            'priority' => TaskPriorityEnum::NONE->value,
            'label_ids' => [$foreignLabel->id],
        ]);

        $response->assertSessionHasErrors('label_ids.0');
    }

    public function test_store_syncs_labels(): void
    {
        $user = $this->user();
        $label = Label::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Task with label',
            'date' => now()->toDateString(),
            'priority' => TaskPriorityEnum::NONE->value,
            'label_ids' => [$label->id],
        ]);

        $task = Task::where('user_id', $user->id)->first();
        $this->assertTrue($task->labels->contains($label));
    }

    // Update

    public function test_update_changes_task_fields(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);
        $tomorrow = now()->addDay()->toDateString();

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Updated title',
            'date' => $tomorrow,
            'priority' => TaskPriorityEnum::HIGH->value,
            'label_ids' => [],
        ]);

        $response->assertRedirect();
        $fresh = $task->fresh();
        $this->assertEquals('Updated title', $fresh->title);
        $this->assertEquals($tomorrow, $fresh->date->toDateString());
        $this->assertEquals(TaskPriorityEnum::HIGH, $fresh->priority);
    }

    public function test_update_forbidden_for_other_users_task(): void
    {
        $task = Task::factory()->dueToday()->create();

        $response = $this->actingAs($this->user())->put(route('tasks.update', $task), [
            'title' => 'Hijacked',
            'date' => now()->toDateString(),
            'priority' => TaskPriorityEnum::NONE->value,
            'label_ids' => [],
        ]);

        $response->assertForbidden();
    }

    public function test_update_rejects_past_date(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Task',
            'date' => now()->subDay()->toDateString(),
            'priority' => TaskPriorityEnum::NONE->value,
            'label_ids' => [],
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_update_syncs_labels(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);
        $label = Label::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => $task->title,
            'date' => $task->date->toDateString(),
            'priority' => TaskPriorityEnum::NONE->value,
            'label_ids' => [$label->id],
        ]);

        $this->assertTrue($task->fresh()->labels->contains($label));
    }

    // Update status

    public function test_update_status_changes_task_status(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('tasks.status.update', $task), [
            'status' => TaskStatusEnum::COMPLETED->value,
        ]);

        $response->assertRedirect();
        $this->assertEquals(TaskStatusEnum::COMPLETED, $task->fresh()->status);
    }

    public function test_update_status_rejects_non_today_task(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueTomorrow()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('tasks.status.update', $task), [
            'status' => TaskStatusEnum::COMPLETED->value,
        ]);

        $response->assertForbidden();
    }

    public function test_update_status_rejects_invalid_enum_value(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('tasks.status.update', $task), [
            'status' => 'not-a-real-status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    // Update labels

    public function test_update_labels_syncs_correctly(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);
        $label = Label::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('tasks.labels.update', $task), [
            'label_ids' => [$label->id],
        ]);

        $response->assertRedirect();
        $this->assertTrue($task->fresh()->labels->contains($label));
    }

    public function test_update_labels_forbidden_for_other_users_task(): void
    {
        $task = Task::factory()->dueToday()->create();

        $response = $this->actingAs($this->user())->put(route('tasks.labels.update', $task), [
            'label_ids' => [],
        ]);

        $response->assertForbidden();
    }

    // Destroy

    public function test_destroy_deletes_task(): void
    {
        $user = $this->user();
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_destroy_forbidden_for_other_users_task(): void
    {
        $task = Task::factory()->dueToday()->create();

        $response = $this->actingAs($this->user())->delete(route('tasks.destroy', $task));

        $response->assertForbidden();
    }

    // Materialize

    public function test_materialize_creates_task_from_template(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('tasks.materialize', $template), [
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'recurring_task_template_id' => $template->id,
            'title' => $template->title,
            'status' => TaskStatusEnum::TO_DO->value,
        ]);
    }

    public function test_materialize_is_idempotent(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);
        $date = now()->toDateString();

        $this->actingAs($user)->post(route('tasks.materialize', $template), ['date' => $date]);
        $this->actingAs($user)->post(route('tasks.materialize', $template), ['date' => $date]);

        $this->assertDatabaseCount('tasks', 1);
    }

    public function test_materialize_forbidden_for_other_users_template(): void
    {
        $template = RecurringTaskTemplate::factory()->create();

        $response = $this->actingAs($this->user())->post(route('tasks.materialize', $template), [
            'date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
    }

    public function test_materialize_syncs_template_labels_on_first_creation(): void
    {
        $user = $this->user();
        $template = RecurringTaskTemplate::factory()->create(['user_id' => $user->id]);
        $label = Label::factory()->create(['user_id' => $user->id]);
        $template->labels()->sync([$label->id]);

        $this->actingAs($user)->post(route('tasks.materialize', $template), [
            'date' => now()->toDateString(),
        ]);

        $task = Task::where('recurring_task_template_id', $template->id)->first();
        $this->assertTrue($task->labels->contains($label));
    }
}

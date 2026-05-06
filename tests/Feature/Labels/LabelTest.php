<?php

namespace Tests\Feature\Labels;

use App\Models\Label;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LabelTest extends TestCase
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

    // Index

    public function test_index_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user())->get(route('labels.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('labels/Labels')
        );
    }

    public function test_index_redirects_guests(): void
    {
        $this->get(route('labels.index'))->assertRedirect(route('login'));
    }

    // Show

    public function test_show_renders_label_page(): void
    {
        $user = $this->user();
        $label = Label::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('labels.show', $label));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('labels/ShowLabel')
            ->where('label.id', $label->id)
        );
    }

    public function test_show_includes_todays_and_future_tasks_for_label(): void
    {
        $user = $this->user();
        $label = Label::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->dueToday()->create(['user_id' => $user->id]);
        $task->labels()->attach($label);

        $response = $this->actingAs($user)->get(route('labels.show', $label));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('labels/ShowLabel')
            ->has('label.tasks', fn (AssertableInertia $tasks) => $tasks->has(1)
                ->first(fn (AssertableInertia $t) => $t->where('id', $task->id)->etc()
                )
            )
        );
    }

    public function test_show_forbidden_for_other_users_label(): void
    {
        $label = Label::factory()->create();

        $response = $this->actingAs($this->user())->get(route('labels.show', $label));

        $response->assertForbidden();
    }

    // Store

    public function test_store_creates_label(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->post(route('labels.store'), [
            'name' => 'Work',
        ]);

        $response->assertRedirect(route('labels.index'));
        $this->assertDatabaseHas('labels', [
            'user_id' => $user->id,
            'name' => 'Work',
        ]);
    }

    public function test_store_redirects_guests(): void
    {
        $this->post(route('labels.store'), ['name' => 'Work'])->assertRedirect(route('login'));
    }

    public function test_store_requires_name(): void
    {
        $response = $this->actingAs($this->user())->post(route('labels.store'), []);

        $response->assertSessionHasErrors('name');
    }

    public function test_store_enforces_unique_name_per_user(): void
    {
        $user = $this->user();
        Label::factory()->create(['user_id' => $user->id, 'name' => 'Existing']);

        $response = $this->actingAs($user)->post(route('labels.store'), [
            'name' => 'Existing',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_store_allows_same_name_for_different_user(): void
    {
        Label::factory()->create(['name' => 'Work']);

        $user = $this->user();
        $response = $this->actingAs($user)->post(route('labels.store'), [
            'name' => 'Work',
        ]);

        $response->assertRedirect(route('labels.index'));
        $this->assertDatabaseHas('labels', ['user_id' => $user->id, 'name' => 'Work']);
    }

    // Update

    public function test_update_changes_label_name(): void
    {
        $user = $this->user();
        $label = Label::factory()->create(['user_id' => $user->id, 'name' => 'Old']);

        $response = $this->actingAs($user)->put(route('labels.update', $label), [
            'name' => 'New',
        ]);

        $response->assertRedirect(route('labels.index'));
        $this->assertEquals('New', $label->fresh()->name);
    }

    public function test_update_forbidden_for_other_users_label(): void
    {
        $label = Label::factory()->create();

        $response = $this->actingAs($this->user())->put(route('labels.update', $label), [
            'name' => 'Hijacked',
        ]);

        $response->assertForbidden();
    }

    public function test_update_enforces_unique_name_ignoring_self(): void
    {
        $user = $this->user();
        $label = Label::factory()->create(['user_id' => $user->id, 'name' => 'Same']);

        $response = $this->actingAs($user)->put(route('labels.update', $label), [
            'name' => 'Same',
        ]);

        $response->assertRedirect(route('labels.index'));
        $this->assertEquals('Same', $label->fresh()->name);
    }

    public function test_update_rejects_name_already_used_by_another_label(): void
    {
        $user = $this->user();
        Label::factory()->create(['user_id' => $user->id, 'name' => 'Taken']);
        $label = Label::factory()->create(['user_id' => $user->id, 'name' => 'Mine']);

        $response = $this->actingAs($user)->put(route('labels.update', $label), [
            'name' => 'Taken',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // Destroy

    public function test_destroy_deletes_label(): void
    {
        $user = $this->user();
        $label = Label::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('labels.destroy', $label));

        $response->assertRedirect(route('labels.index'));
        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }

    public function test_destroy_forbidden_for_other_users_label(): void
    {
        $label = Label::factory()->create();

        $response = $this->actingAs($this->user())->delete(route('labels.destroy', $label));

        $response->assertForbidden();
    }
}

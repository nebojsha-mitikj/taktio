<?php

declare(strict_types=1);

namespace Tests\Feature\Plan;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlanRedirectTest extends TestCase
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

    public function test_plan_index_redirects_to_current_month(): void
    {
        $this->actingAs($this->user())
            ->get(route('plan.index'))
            ->assertRedirect(route('plan.show', ['year' => 2026, 'month' => '05']));
    }

    public function test_plan_index_uses_zero_padded_month(): void
    {
        $this->actingAs($this->user())
            ->get(route('plan.index'))
            ->assertRedirectContains('/plan/2026/05');
    }

    public function test_plan_index_redirects_guests(): void
    {
        $this->get(route('plan.index'))->assertRedirect(route('login'));
    }

    public function test_plan_show_renders_for_authenticated_user(): void
    {
        $this->actingAs($this->user())
            ->get(route('plan.show', ['year' => 2026, 'month' => '05']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('plan/Plan')
                ->where('year', 2026)
                ->where('month', 5)
                ->where('mode', 'current')
            );
    }

    public function test_plan_show_redirects_guests(): void
    {
        $this->get(route('plan.show', ['year' => 2026, 'month' => '05']))
            ->assertRedirect(route('login'));
    }

    public function test_plan_show_returns_404_for_invalid_month(): void
    {
        $this->actingAs($this->user())
            ->get(route('plan.show', ['year' => 2026, 'month' => 13]))
            ->assertNotFound();
    }

    public function test_plan_show_returns_404_for_invalid_year(): void
    {
        $this->actingAs($this->user())
            ->get(route('plan.show', ['year' => 1900, 'month' => 1]))
            ->assertNotFound();
    }
}

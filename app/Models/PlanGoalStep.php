<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $plan_goal_id
 * @property string $title
 * @property bool $completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Relationships
 * @property-read PlanGoal $goal
 */
class PlanGoalStep extends Model
{
    protected $table = 'plan_goal_steps';

    protected $fillable = [
        'plan_goal_id',
        'title',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(PlanGoal::class, 'plan_goal_id');
    }
}

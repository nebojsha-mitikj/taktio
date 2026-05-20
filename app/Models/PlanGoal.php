<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $month_plan_id
 * @property string $title
 * @property bool $completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Relationships
 * @property-read MonthPlan $monthPlan
 * @property-read Collection<int, PlanGoalStep> $steps
 */
class PlanGoal extends Model
{
    protected $table = 'plan_goals';

    protected $fillable = [
        'month_plan_id',
        'title',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function monthPlan(): BelongsTo
    {
        return $this->belongsTo(MonthPlan::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(PlanGoalStep::class)->orderBy('created_at');
    }
}

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
 * @property int $user_id
 * @property int $year
 * @property int $month
 * @property string|null $main_goal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Relationships
 * @property-read User $user
 * @property-read Collection<int, PlanGoal> $goals
 */
class MonthPlan extends Model
{
    protected $table = 'month_plans';

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'main_goal',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(PlanGoal::class)->orderBy('created_at');
    }
}

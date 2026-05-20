<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $recurring_task_template_id
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Relationships
 * @property-read RecurringTaskTemplate $template
 *
 * @method static Builder|RecurringTaskTemplatePeriod query()
 */
class RecurringTaskTemplatePeriod extends Model
{
    protected $table = 'recurring_task_template_periods';

    protected $fillable = [
        'recurring_task_template_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(RecurringTaskTemplate::class, 'recurring_task_template_id');
    }
}

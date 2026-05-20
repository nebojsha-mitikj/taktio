<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LabelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Relationships
 * @property-read User $user
 * @property-read Collection<int, Task> $tasks
 * @property-read Collection<int, RecurringTaskTemplate> $recurringTaskTemplates
 *
 * @method static Builder|Label query()
 */
class Label extends Model
{
    /** @use HasFactory<LabelFactory> */
    use HasFactory;

    protected $table = 'labels';

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'label_task')
            ->withTimestamps();
    }

    public function recurringTaskTemplates(): BelongsToMany
    {
        return $this->belongsToMany(RecurringTaskTemplate::class, 'label_recurring_task_template')
            ->withTimestamps();
    }
}

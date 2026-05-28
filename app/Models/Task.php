<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriorityEnum;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $recurring_task_template_id
 * @property string $title
 * @property string|null $description
 * @property Carbon $date
 * @property bool $completed
 * @property TaskPriorityEnum $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Relationships
 * @property-read User $user
 * @property-read RecurringTaskTemplate|null $recurringTaskTemplate
 * @property-read Collection<int, Label> $labels
 *
 * @method static Builder|Task query()
 */
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'user_id',
        'recurring_task_template_id',
        'title',
        'description',
        'date',
        'completed',
        'priority',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'priority' => TaskPriorityEnum::class,
        'date' => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recurringTaskTemplate(): BelongsTo
    {
        return $this->belongsTo(RecurringTaskTemplate::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'label_task')
            ->withTimestamps();
    }

    public function scopeOrdered(Builder $query, bool $latestFirst = false): Builder
    {
        return $query
            ->orderBy('date', $latestFirst ? 'DESC' : 'ASC')
            ->orderBy('completed', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public static function sortCollection(SupportCollection $tasks): SupportCollection
    {
        $priorityOrder = array_flip(TaskPriorityEnum::ordered());

        return $tasks->sort(function ($a, $b) use ($priorityOrder) {
            $dateA = $a->date->timestamp;
            $dateB = $b->date->timestamp;

            if ($dateA !== $dateB) {
                return $dateA <=> $dateB;
            }

            if ($a->completed !== $b->completed) {
                return $a->completed <=> $b->completed;
            }

            $priorityA = $priorityOrder[$a->priority->value];
            $priorityB = $priorityOrder[$b->priority->value];

            if ($priorityA !== $priorityB) {
                return $priorityA <=> $priorityB;
            }

            return $b->created_at <=> $a->created_at;
        })->values();
    }
}

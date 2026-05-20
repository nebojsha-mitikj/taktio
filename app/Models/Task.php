<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
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
 * @property TaskStatusEnum $status
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
        'status',
        'priority',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
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
        $statusOrder = TaskStatusEnum::ordered();
        $cases = collect($statusOrder)
            ->map(fn ($value, $i) => "WHEN '{$value}' THEN {$i}")
            ->implode(' ');

        return $query
            ->orderBy('date', $latestFirst ? 'DESC' : 'ASC')
            ->orderByRaw("CASE status {$cases} END")
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public static function sortCollection(SupportCollection $tasks): SupportCollection
    {
        $statusOrder = array_flip(TaskStatusEnum::ordered());
        $priorityOrder = array_flip(TaskPriorityEnum::ordered());

        return $tasks->sort(function ($a, $b) use ($statusOrder, $priorityOrder) {
            $dateA = $a->date->timestamp;
            $dateB = $b->date->timestamp;

            if ($dateA !== $dateB) {
                return $dateA <=> $dateB;
            }

            $statusA = $statusOrder[$a->status->value];
            $statusB = $statusOrder[$b->status->value];

            if ($statusA !== $statusB) {
                return $statusA <=> $statusB;
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

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskRecurEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\WeekdayEnum;
use Carbon\Carbon;
use Database\Factories\RecurringTaskTemplateFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as SupportCollection;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property TaskRecurEnum $recur
 * @property TaskPriorityEnum $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Relationships
 * @property-read User $user
 * @property-read Collection<int, RecurringTaskTemplateWeekday> $weekdays
 * @property-read Collection<int, RecurringTaskTemplatePeriod> $periods
 * @property-read Collection<int, Task> $tasks
 * @property-read Collection<int, Label> $labels
 *
 * @method static Builder|RecurringTaskTemplate query()
 */
class RecurringTaskTemplate extends Model
{
    /** @use HasFactory<RecurringTaskTemplateFactory> */
    use HasFactory;

    protected $appends = ['is_active'];

    protected $table = 'recurring_task_templates';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'recur',
        'priority',
    ];

    protected $casts = [
        'recur' => TaskRecurEnum::class,
        'priority' => TaskPriorityEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(RecurringTaskTemplatePeriod::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'label_recurring_task_template')
            ->withTimestamps();
    }

    public function weekdays(): HasMany
    {
        return $this->hasMany(RecurringTaskTemplateWeekday::class);
    }

    public function syncWeekdays(array $weekdays): void
    {
        $this->weekdays()->delete();
        if ($this->recur !== TaskRecurEnum::WEEKLY) {
            return;
        }
        foreach ($weekdays as $weekday) {
            $this->weekdays()->create(['weekday' => $weekday]);
        }
    }

    public function saveFromPayload(array $data): void
    {
        $labelIds = $data['label_ids'] ?? [];
        $weekdays = $data['weekdays'] ?? [];
        unset($data['label_ids'], $data['weekdays']);

        $this->fill($data)->save();

        $this->labels()->sync($labelIds);
        $this->syncWeekdays($weekdays);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('periods', function ($query) {
            $query->whereNull('end_date');
        });
    }

    public function scopeBelongsToUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->periods()->whereNull('end_date')->exists();
    }

    public function toVirtualTask(Carbon $date): Task
    {
        $task = new Task([
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $date->toDateString(),
            'priority' => $this->priority,
            'status' => TaskStatusEnum::TO_DO,
            'recurring_task_template_id' => $this->id,
        ]);
        $task->setRelation('labels', $this->labels);
        $task->is_virtual = true;
        $task->created_at = now();
        $task->updated_at = $task->created_at;

        return $task;
    }

    public static function generateVirtualTasks(
        SupportCollection $templates,
        SupportCollection $existingTasks,
        Carbon $start,
        Carbon $end
    ): SupportCollection {
        $existingByTemplateAndDate = $existingTasks
            ->filter(fn (Task $t) => $t->recurring_task_template_id !== null)
            ->groupBy(fn (Task $t) => $t->recurring_task_template_id.'_'.$t->date->toDateString());

        $windowDates = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $windowDates->push($date->copy());
        }

        $extraDates = $existingTasks
            ->filter(fn (Task $t) => $t->date->gt($end))
            ->pluck('date')
            ->unique(fn ($date) => $date->toDateString());

        $virtualTasks = collect();
        foreach ($windowDates->concat($extraDates) as $date) {
            foreach ($templates as $template) {
                if (! $template->isDueOnDate($date)) {
                    continue;
                }
                $key = $template->id.'_'.$date->toDateString();
                if ($existingByTemplateAndDate->has($key)) {
                    continue;
                }
                $virtualTasks->push($template->toVirtualTask($date->copy()));
            }
        }

        return $virtualTasks;
    }

    public function isDueOnDate(Carbon $date): bool
    {
        return match ($this->recur) {
            TaskRecurEnum::DAILY => true,
            TaskRecurEnum::WEEKDAYS => $date->isWeekday(),
            TaskRecurEnum::WEEKENDS => $date->isWeekend(),
            TaskRecurEnum::WEEKLY => $this->weekdays
                ->pluck('weekday')
                ->contains(WeekdayEnum::from(strtolower($date->englishDayOfWeek))),
        };
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
    }
}

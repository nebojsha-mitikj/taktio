<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\MaterializeRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Requests\UpdateTaskLabelsRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\RecurringTaskTemplate;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{

    private function tasksQuery(?int $userId = null, bool $latestFirst = false): Builder
    {
        $userId ??= auth()->id();
        return Task::query()
            ->with('labels')
            ->where('user_id', $userId)
            ->ordered($latestFirst);
    }

    public function today(): Response
    {
        $today = Carbon::today();

        $tasks = $this->tasksQuery()
            ->whereDate('date', $today)
            ->get();

        /** @var Collection $recurring */
        $recurring = RecurringTaskTemplate::active()
            ->belongsToUser(auth()->id())
            ->with(['labels', 'weekdays'])
            ->get();

        $recurring = $recurring->filter(function (RecurringTaskTemplate $recurringTaskTemplate) use ($today) {
            return $recurringTaskTemplate->isDueOnDate($today);
        });

        $existingTemplateIds = $tasks
            ->pluck('recurring_task_template_id')
            ->filter()
            ->unique();

        $virtualTasks = $recurring
            ->reject(fn(RecurringTaskTemplate $t) => $existingTemplateIds->contains($t->id))
            ->map(fn(RecurringTaskTemplate $t) => $t->toVirtualTask($today));

        $allTasks = Task::sortCollection($tasks->concat($virtualTasks));

        return Inertia::render('tasks/Today', [
            'tasks' => $allTasks
        ]);
    }

    public function upcoming(): Response
    {
        $start = today()->addDay();
        $end = today()->addDays(7);

        $tasks = $this->tasksQuery()
            ->where('date', '>=', $start->toDateString())
            ->get();

        /** @var Collection $recurring */
        $recurring = RecurringTaskTemplate::active()
            ->belongsToUser(auth()->id())
            ->with(['labels', 'weekdays'])
            ->get();

        $virtualTasks = RecurringTaskTemplate::generateVirtualTasks($recurring, $tasks, $start, $end);

        $tasksByDate = $tasks->concat($virtualTasks->all())
            ->groupBy(fn(Task $t) => $t->date->toDateString())
            ->map(fn($group) => Task::sortCollection($group))
            ->sortKeys();

        return Inertia::render('tasks/Upcoming', [
            'tasksByDate' => $tasksByDate,
        ]);
    }

    public function history(): Response
    {
        return Inertia::render('tasks/History', [
            'tasksByDate' => $this->tasksQuery(latestFirst: true)
                ->whereDate('date', '<', today())
                ->get()
                ->groupBy(fn($task) => $task->date->toDateString())
        ]);
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return back()->with('success', 'Task deleted successfully.');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();
        $labelIds = $data['label_ids'] ?? [];
        unset($data['label_ids']);
        $task->update($data);
        $task->labels()->sync($labelIds);
        return back()->with('success', 'Task updated successfully.');
    }

    public function updateStatus(UpdateStatusRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());
        return back()->with('success', 'Task status updated successfully.');
    }

    public function updateLabels(UpdateTaskLabelsRequest $request, Task $task): RedirectResponse
    {
        $task->labels()->sync($request->label_ids);
        return back()->with('success', 'Task labels updated successfully.');
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $labelIds = $data['label_ids'] ?? [];
        unset($data['label_ids']);
        $task = $request->user()->tasks()->create($data);
        $task->labels()->sync($labelIds);
        return back()->with('success', 'Task created successfully.');
    }

    public function materialize(MaterializeRequest $request, RecurringTaskTemplate $template): RedirectResponse
    {
        $searchAttributes = [
            'recurring_task_template_id' => $template->id,
            'date' => Carbon::parse($request->date)->setTimezone(date_default_timezone_get())->toDateString(),
            'user_id' => auth()->id(),
        ];

        $createAttributes = [
            'title' => $template->title,
            'description' => $template->description,
            'priority' => $template->priority,
            'status' => TaskStatusEnum::TO_DO,
        ];

        try {
            $task = Task::firstOrCreate($searchAttributes, $createAttributes);
        } catch (UniqueConstraintViolationException) {
            $task = Task::firstWhere($searchAttributes);
        }

        if (isset($request->status)) {
            $task->update(['status' => $request->status]);
        }

        if ($task->wasRecentlyCreated) {
            $task->labels()->sync($template->load('labels')->labels->pluck('id'));
        }

        return back()->with('success', 'Task updated successfully.');
    }
}

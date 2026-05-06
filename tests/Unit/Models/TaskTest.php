<?php

namespace Tests\Unit\Models;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Tests\TestCase;

class TaskTest extends TestCase
{
    private function makeSortableTask(TaskStatusEnum $status, TaskPriorityEnum $priority, string $date = '2026-05-05'): Task
    {
        $task = new Task([
            'title' => 'test',
            'status' => $status,
            'priority' => $priority,
            'date' => $date,
        ]);
        $task->created_at = now();
        $task->updated_at = now();

        return $task;
    }

    public function test_sort_puts_in_progress_before_to_do(): void
    {
        $toDo = $this->makeSortableTask(TaskStatusEnum::TO_DO, TaskPriorityEnum::NONE);
        $inProgress = $this->makeSortableTask(TaskStatusEnum::IN_PROGRESS, TaskPriorityEnum::NONE);

        $sorted = Task::sortCollection(collect([$toDo, $inProgress]));

        $this->assertSame(TaskStatusEnum::IN_PROGRESS, $sorted->first()->status);
    }

    public function test_sort_puts_completed_last(): void
    {
        $completed = $this->makeSortableTask(TaskStatusEnum::COMPLETED, TaskPriorityEnum::NONE);
        $toDo = $this->makeSortableTask(TaskStatusEnum::TO_DO, TaskPriorityEnum::NONE);
        $inProgress = $this->makeSortableTask(TaskStatusEnum::IN_PROGRESS, TaskPriorityEnum::NONE);

        $sorted = Task::sortCollection(collect([$completed, $toDo, $inProgress]));

        $this->assertSame(TaskStatusEnum::COMPLETED, $sorted->last()->status);
        $this->assertSame(TaskStatusEnum::IN_PROGRESS, $sorted->first()->status);
    }

    public function test_sort_orders_by_priority_descending_within_same_status(): void
    {
        $low = $this->makeSortableTask(TaskStatusEnum::TO_DO, TaskPriorityEnum::LOW);
        $high = $this->makeSortableTask(TaskStatusEnum::TO_DO, TaskPriorityEnum::HIGH);
        $medium = $this->makeSortableTask(TaskStatusEnum::TO_DO, TaskPriorityEnum::MEDIUM);

        $sorted = Task::sortCollection(collect([$low, $medium, $high]));

        $this->assertSame(TaskPriorityEnum::HIGH, $sorted->values()[0]->priority);
        $this->assertSame(TaskPriorityEnum::MEDIUM, $sorted->values()[1]->priority);
        $this->assertSame(TaskPriorityEnum::LOW, $sorted->values()[2]->priority);
    }

    public function test_sort_orders_by_date_ascending(): void
    {
        $later = $this->makeSortableTask(TaskStatusEnum::TO_DO, TaskPriorityEnum::NONE, '2026-05-10');
        $earlier = $this->makeSortableTask(TaskStatusEnum::TO_DO, TaskPriorityEnum::NONE, '2026-05-06');

        $sorted = Task::sortCollection(collect([$later, $earlier]));

        $this->assertEquals('2026-05-06', $sorted->first()->date->toDateString());
    }
}

<?php

namespace Tests\Unit\Models;

use App\Enums\TaskPriorityEnum;
use App\Models\Task;
use Tests\TestCase;

class TaskTest extends TestCase
{
    private function makeSortableTask(bool $completed, TaskPriorityEnum $priority, string $date = '2026-05-05'): Task
    {
        $task = new Task([
            'title' => 'test',
            'completed' => $completed,
            'priority' => $priority,
            'date' => $date,
        ]);
        $task->created_at = now();
        $task->updated_at = now();

        return $task;
    }

    public function test_sort_puts_incomplete_before_completed(): void
    {
        $incomplete = $this->makeSortableTask(false, TaskPriorityEnum::NONE);
        $completed = $this->makeSortableTask(true, TaskPriorityEnum::NONE);

        $sorted = Task::sortCollection(collect([$completed, $incomplete]));

        $this->assertFalse($sorted->first()->completed);
    }

    public function test_sort_puts_completed_last(): void
    {
        $completed = $this->makeSortableTask(true, TaskPriorityEnum::NONE);
        $incomplete = $this->makeSortableTask(false, TaskPriorityEnum::NONE);

        $sorted = Task::sortCollection(collect([$completed, $incomplete]));

        $this->assertTrue($sorted->last()->completed);
        $this->assertFalse($sorted->first()->completed);
    }

    public function test_sort_orders_by_priority_descending_within_same_status(): void
    {
        $low = $this->makeSortableTask(false, TaskPriorityEnum::LOW);
        $high = $this->makeSortableTask(false, TaskPriorityEnum::HIGH);
        $medium = $this->makeSortableTask(false, TaskPriorityEnum::MEDIUM);

        $sorted = Task::sortCollection(collect([$low, $medium, $high]));

        $this->assertSame(TaskPriorityEnum::HIGH, $sorted->values()[0]->priority);
        $this->assertSame(TaskPriorityEnum::MEDIUM, $sorted->values()[1]->priority);
        $this->assertSame(TaskPriorityEnum::LOW, $sorted->values()[2]->priority);
    }

    public function test_sort_orders_by_date_ascending(): void
    {
        $later = $this->makeSortableTask(false, TaskPriorityEnum::NONE, '2026-05-10');
        $earlier = $this->makeSortableTask(false, TaskPriorityEnum::NONE, '2026-05-06');

        $sorted = Task::sortCollection(collect([$later, $earlier]));

        $this->assertEquals('2026-05-06', $sorted->first()->date->toDateString());
    }
}

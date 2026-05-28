<?php

declare(strict_types=1);

use App\Enums\TaskPriorityEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('recurring_task_template_id')
                ->nullable()
                ->constrained('recurring_task_templates')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->enum('status', ['to-do', 'in-progress', 'completed'])->default('to-do');
            $table->enum('priority', TaskPriorityEnum::values())->default(TaskPriorityEnum::NONE->value);

            $table->unique(['recurring_task_template_id', 'date']);
            $table->index(['user_id', 'date']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

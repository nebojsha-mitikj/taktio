<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('label_recurring_task_template', function (Blueprint $table) {
            $table->foreignId('recurring_task_template_id')
                ->constrained('recurring_task_templates')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('label_id')
                ->constrained('labels')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->primary(['recurring_task_template_id', 'label_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label_recurring_task_template');
    }
};

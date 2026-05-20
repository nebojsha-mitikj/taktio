<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_task_template_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_task_template_id');
            $table->foreign('recurring_task_template_id', 'rtp_template_fk')
                ->references('id')
                ->on('recurring_task_templates')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unique(['start_date', 'recurring_task_template_id'], 'rtp_start_template_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_task_template_periods');
    }
};

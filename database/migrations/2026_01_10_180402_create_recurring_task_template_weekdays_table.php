<?php

declare(strict_types=1);

use App\Enums\WeekdayEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_task_template_weekdays', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('recurring_task_template_id');

            $table->foreign('recurring_task_template_id', 'rttw_template_fk')
                ->references('id')
                ->on('recurring_task_templates')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('weekday', WeekdayEnum::values());

            $table->unique(['recurring_task_template_id', 'weekday'], 'rttw_weekday_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_task_template_weekdays');
    }
};

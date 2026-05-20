<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_goals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('month_plan_id')
                ->constrained('month_plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('title', 500);
            $table->boolean('completed')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_goals');
    }
};

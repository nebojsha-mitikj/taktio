<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('month_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('main_goal', 500)->nullable();

            $table->unique(['user_id', 'year', 'month']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('month_plans');
    }
};

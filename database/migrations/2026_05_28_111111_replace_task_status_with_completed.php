<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('completed')->default(false)->after('date');
        });

        DB::table('tasks')->where('status', 'completed')->update(['completed' => true]);

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['to-do', 'in-progress', 'completed'])->default('to-do')->after('date');
        });

        DB::table('tasks')->where('completed', true)->update(['status' => 'completed']);
        DB::table('tasks')->where('completed', false)->update(['status' => 'to-do']);

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('completed');
        });
    }
};

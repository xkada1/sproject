<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'work_date']);
            $table->index(['user_id', 'work_date']);

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

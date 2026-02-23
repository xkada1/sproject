<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('action'); // set_stock, reserve, unreserve, deduct
            $table->integer('qty')->default(0);
            $table->integer('stock_before')->nullable();
            $table->integer('stock_after')->nullable();
            $table->integer('reserved_before')->default(0);
            $table->integer('reserved_after')->default(0);

            $table->string('reference')->nullable(); // e.g. order#12
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hold/resume
            // status will include: held, pending, processing, completed, cancelled

            // Payments
            $table->string('payment_method')->nullable()->after('discount'); // cash, gcash, card
            $table->decimal('amount_tendered', 10, 2)->nullable()->after('payment_method');
            $table->decimal('change_amount', 10, 2)->nullable()->after('amount_tendered');
            $table->timestamp('paid_at')->nullable()->after('change_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'amount_tendered', 'change_amount', 'paid_at']);
        });
    }
};
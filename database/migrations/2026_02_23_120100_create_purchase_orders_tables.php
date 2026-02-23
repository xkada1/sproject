<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->index();
                $table->unsignedBigInteger('supplier_id')->nullable()->index();
                $table->string('po_number')->nullable()->unique();
                $table->string('status')->default('draft')->index(); // draft, ordered, received, cancelled
                $table->text('notes')->nullable();
                $table->dateTime('ordered_at')->nullable();
                $table->dateTime('received_at')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
                $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_order_id')->index();
                $table->string('product_name');
                $table->integer('quantity');
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->decimal('line_total', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
        // NOTE: Some MySQL setups may mis-report Schema::hasColumn() inside the Blueprint callback.
        // We check first, then only run Schema::table() when we actually need to add something.
        $needsStock = !Schema::hasColumn('products', 'stock');
        $needsReserved = !Schema::hasColumn('products', 'reserved_stock');

        if (!$needsStock && !$needsReserved) {
            return;
        }

        Schema::table('products', function (Blueprint $table) use ($needsStock, $needsReserved) {
            if ($needsStock) {
                $table->integer('stock')->nullable()->after('price');
            }

            if ($needsReserved) {
                $table->integer('reserved_stock')->default(0)->after($needsStock ? 'stock' : 'price');
            }
        });
}

 public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        if (Schema::hasColumn('products', 'reserved_stock')) {
            $table->dropColumn('reserved_stock');
        }

        if (Schema::hasColumn('products', 'stock')) {
            $table->dropColumn('stock');
        }
    });
}
};  
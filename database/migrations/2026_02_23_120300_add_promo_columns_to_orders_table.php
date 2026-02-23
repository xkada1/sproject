<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'promo_code')) {
                $table->string('promo_code')->nullable()->after('discount');
            }
            if (!Schema::hasColumn('orders', 'promo_amount')) {
                $table->decimal('promo_amount', 10, 2)->default(0)->after('promo_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'promo_amount')) $table->dropColumn('promo_amount');
            if (Schema::hasColumn('orders', 'promo_code')) $table->dropColumn('promo_code');
        });
    }
};

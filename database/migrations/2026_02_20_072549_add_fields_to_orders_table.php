<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_type', ['dine-in', 'takeout', 'delivery'])->default('dine-in')->after('status');
            $table->text('notes')->nullable()->after('order_type');
            $table->decimal('discount', 5, 2)->default(0)->after('notes');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'notes', 'discount']);
        });
    }
};
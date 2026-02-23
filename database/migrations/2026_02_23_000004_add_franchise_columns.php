<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // --- Users: branch_id (optional)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('role')->constrained('branches')->nullOnDelete();
            }
        });

        // --- Tables: branch_id + qr_token
        Schema::table('tables', function (Blueprint $table) {
            if (!Schema::hasColumn('tables', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
            }
            if (!Schema::hasColumn('tables', 'qr_token')) {
                $table->string('qr_token', 60)->nullable()->unique()->after('status');
            }
        });

        // --- Products: branch_id + supplier_id + cost_price
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('category_id')->constrained('suppliers')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->default(0)->after('price');
            }
        });

        // --- Orders: branch_id
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
            }
        });

        // Seed a default branch if none exists (safe for existing databases)
        if (Schema::hasTable('branches')) {
            $has = DB::table('branches')->count();
            if ($has === 0) {
                DB::table('branches')->insert([
                    'name' => 'Main Branch',
                    'code' => 'MAIN',
                    'address' => null,
                    'phone' => null,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Backfill branch_id + qr_token for existing rows
        $branchId = DB::table('branches')->orderBy('id')->value('id');
        if ($branchId) {
            if (Schema::hasTable('tables') && Schema::hasColumn('tables', 'branch_id')) {
                DB::table('tables')->whereNull('branch_id')->update(['branch_id' => $branchId]);
                // Generate missing qr tokens
                $tables = DB::table('tables')->whereNull('qr_token')->get(['id']);
                foreach ($tables as $t) {
                    DB::table('tables')->where('id', $t->id)->update(['qr_token' => Str::random(40)]);
                }
            }
            if (Schema::hasTable('products') && Schema::hasColumn('products', 'branch_id')) {
                DB::table('products')->whereNull('branch_id')->update(['branch_id' => $branchId]);
            }
            if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'branch_id')) {
                DB::table('orders')->whereNull('branch_id')->update(['branch_id' => $branchId]);
            }
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'branch_id')) {
                DB::table('users')->whereNull('branch_id')->update(['branch_id' => $branchId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'branch_id')) {
                $table->dropConstrainedForeignId('branch_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }
            if (Schema::hasColumn('products', 'branch_id')) {
                $table->dropConstrainedForeignId('branch_id');
            }
            if (Schema::hasColumn('products', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
        });

        Schema::table('tables', function (Blueprint $table) {
            if (Schema::hasColumn('tables', 'branch_id')) {
                $table->dropConstrainedForeignId('branch_id');
            }
            if (Schema::hasColumn('tables', 'qr_token')) {
                $table->dropColumn('qr_token');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'branch_id')) {
                $table->dropConstrainedForeignId('branch_id');
            }
        });
    }
};

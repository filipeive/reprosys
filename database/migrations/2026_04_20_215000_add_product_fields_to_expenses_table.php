<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('expenses', 'product_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('notes');
            });
        }

        if (! Schema::hasColumn('expenses', 'quantity')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->integer('quantity')->nullable()->default(1)->after('product_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};

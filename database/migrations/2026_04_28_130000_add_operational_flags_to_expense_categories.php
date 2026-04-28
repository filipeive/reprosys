<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->boolean('is_operational')->default(false)->after('description');
            $table->boolean('is_rent')->default(false)->after('is_operational');
        });

        $categories = DB::table('expense_categories')->select('id', 'name', 'description')->get();

        foreach ($categories as $category) {
            $haystack = mb_strtolower(trim(($category->name ?? '') . ' ' . ($category->description ?? '')));

            $isRent = str_contains($haystack, 'renda') || str_contains($haystack, 'alug');
            $isOperational = $isRent
                || str_contains($haystack, 'material')
                || str_contains($haystack, 'matéria')
                || str_contains($haystack, 'materia')
                || str_contains($haystack, 'energia')
                || str_contains($haystack, 'electric')
                || str_contains($haystack, 'agua')
                || str_contains($haystack, 'água')
                || str_contains($haystack, 'internet')
                || str_contains($haystack, 'transporte')
                || str_contains($haystack, 'combust')
                || str_contains($haystack, 'manuten')
                || str_contains($haystack, 'salario')
                || str_contains($haystack, 'salário');

            DB::table('expense_categories')
                ->where('id', $category->id)
                ->update([
                    'is_operational' => $isOperational,
                    'is_rent' => $isRent,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn(['is_operational', 'is_rent']);
        });
    }
};

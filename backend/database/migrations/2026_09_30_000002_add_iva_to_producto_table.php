<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('producto', 'iva')) {
            Schema::table('producto', function (Blueprint $table) {
                $table->decimal('iva', 5, 2)->default(16.00);
            });
        }
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('iva');
        });
    }
};
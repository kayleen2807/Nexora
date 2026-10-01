<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE producto ALTER COLUMN id_categoria DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE producto ALTER COLUMN id_categoria SET NOT NULL');
    }
};
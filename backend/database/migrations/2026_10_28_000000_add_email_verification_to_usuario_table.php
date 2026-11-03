<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('usuario');

        Schema::table('usuario', function (Blueprint $table) use ($columns) {
            if (! in_array('email_verified_at', $columns, true)) {
                $table->timestamp('email_verified_at')->nullable();
            }

            if (! in_array('email_verification_code', $columns, true)) {
                $table->string('email_verification_code')->nullable();
            }

            if (! in_array('email_verification_expires_at', $columns, true)) {
                $table->timestamp('email_verification_expires_at')->nullable();
            }
        });

        DB::table('usuario')->whereNull('email_verified_at')->update([
            'email_verified_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn([
                'email_verified_at',
                'email_verification_code',
                'email_verification_expires_at',
            ]);
        });
    }
};
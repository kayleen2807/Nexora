<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('invited_by');
            $table->string('token_hash')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->integer('accepted_by')->nullable();
            $table->string('status', 20)->default('sent');
            $table->string('terms_version', 50)->nullable();
            $table->string('privacy_version', 50)->nullable();
            $table->ipAddress('accepted_ip')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_invitations');
    }
};
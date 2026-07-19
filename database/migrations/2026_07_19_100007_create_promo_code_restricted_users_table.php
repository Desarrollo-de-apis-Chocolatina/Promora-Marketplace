<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_code_restricted_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('buyers')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['promo_code_id', 'buyer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_restricted_users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['fixed', 'percent', 'tiered']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'expired'])->default('draft');
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};

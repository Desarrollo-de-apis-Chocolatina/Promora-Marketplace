<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_code_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->enum('rule_type', [
                'min_purchase_amount',
                'eligible_categories',
                'first_order_only',
                'user_usage_limit',
                'global_usage_limit',
                'global_amount_limit',
                'restricted_usage',
            ]);
            $table->json('parameters')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['promo_code_id', 'rule_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_rules');
    }
};

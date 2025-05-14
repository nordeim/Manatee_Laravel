<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('description', 191)->nullable();
            $table->enum('type', ['percentage', 'fixed_amount']);
            $table->integer('value_minor_amount')->unsigned()->comment('For fixed_amount in minor units; for percentage, the % value (e.g., 10 for 10%)');
            $table->string('currency_code', 3)->nullable()->comment('Applicable for fixed_amount type');
            $table->integer('max_uses')->unsigned()->nullable()->comment('Total uses allowed');
            $table->integer('uses_count')->unsigned()->default(0);
            $table->integer('max_uses_per_user')->unsigned()->nullable();
            $table->integer('min_purchase_minor_amount')->unsigned()->nullable();
            $table->string('min_purchase_currency_code', 3)->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'valid_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

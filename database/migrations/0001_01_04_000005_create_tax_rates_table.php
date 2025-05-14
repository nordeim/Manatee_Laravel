<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('e.g., "US-CA Sales Tax", "EU VAT"');
            $table->char('country_code', 2)->comment('ISO 3166-1 alpha-2 country code');
            $table->string('state_code', 10)->nullable()->comment('ISO 3166-2 state/province code');
            $table->string('postal_code_pattern', 50)->nullable()->comment('Regex pattern or specific code');
            $table->string('city', 100)->nullable();
            $table->decimal('rate_percentage', 10, 4); // e.g., 7.2500 for 7.25%
            $table->boolean('is_compound')->default(false);
            $table->unsignedInteger('priority')->default(1);
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['country_code', 'state_code', 'postal_code_pattern', 'city'], 'uq_tax_rates_region');
            $table->index('country_code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};

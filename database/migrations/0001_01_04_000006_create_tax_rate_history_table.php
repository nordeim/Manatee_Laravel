<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rate_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rate_id')->constrained('tax_rates')->cascadeOnDelete();
            $table->decimal('old_rate_percentage', 10, 4)->nullable();
            $table->decimal('new_rate_percentage', 10, 4);
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Timestamp of when the change was made');
            // No updated_at as per sample schema, it's a log
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rate_history');
    }
};

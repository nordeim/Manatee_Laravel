<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email', 191)->nullable();
            $table->json('answers'); // Changed from text to json for better structure
            $table->json('recommendations')->nullable(); // Changed from text to json
            $table->timestamp('created_at')->nullable()->useCurrent();
            // No updated_at as per sample schema, and it's a log-like table

            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id(); // Sample had int auto_increment
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->morphs('auditable'); // Creates auditable_id (BIGINT UNSIGNED) and auditable_type (VARCHAR)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 191)->nullable(); // Sample had 255
            $table->text('details')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            // No updated_at as per sample schema, it's a log
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};

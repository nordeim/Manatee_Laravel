<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('to_email', 191);
            $table->string('subject', 191)->nullable();
            $table->string('email_type', 50)->nullable()->comment('e.g., welcome, password_reset, order_confirmation')->index();
            $table->enum('status', ['sent','failed','pending'])->default('pending');
            $table->text('mailer_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            // No updated_at as per sample schema, it's a log

            $table->index(['to_email', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_log');
    }
};

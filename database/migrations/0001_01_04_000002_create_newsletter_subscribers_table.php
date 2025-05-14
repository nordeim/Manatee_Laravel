<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->timestamp('subscribed_at')->nullable()->useCurrent(); // Maps to created_at
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('token', 64)->nullable()->unique()->comment('For unsubscribe/confirmation link');
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};

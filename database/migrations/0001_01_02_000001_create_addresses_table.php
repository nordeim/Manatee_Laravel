<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id(); // Alias for $table->bigIncrements('id')
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name', 191)->nullable();
            $table->string('last_name', 191)->nullable();
            $table->string('company', 191)->nullable();
            $table->string('address_line1', 191);
            $table->string('address_line2', 191)->nullable();
            $table->string('city', 100);
            $table->string('state', 100)->nullable()->comment('State / Province / Region');
            $table->string('postal_code', 20);
            $table->char('country_code', 2)->comment('ISO 3166-1 alpha-2 country code');
            $table->string('phone', 30)->nullable();
            $table->enum('type', ['billing', 'shipping', 'general'])->nullable()->default('general');
            $table->boolean('is_default_shipping')->default(false);
            $table->boolean('is_default_billing')->default(false);
            $table->timestamps();
        });

        // It's good practice to add foreign keys for default_shipping_address_id and default_billing_address_id
        // on the users table AFTER the addresses table is created.
        // This can be done in a separate migration or by modifying the users table migration
        // if it runs after this one (which it typically doesn't).
        // For now, assuming users table is modified separately to include these columns and then:
        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreign('default_shipping_address_id')->references('id')->on('addresses')->nullOnDelete();
        //     $table->foreign('default_billing_address_id')->references('id')->on('addresses')->nullOnDelete();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropForeign(['default_shipping_address_id']);
        //     $table->dropForeign(['default_billing_address_id']);
        // });
        Schema::dropIfExists('addresses');
    }
};

<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('firstname', 100);
            $table->string('lastname', 100)->nullable()->default('');
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('phone_number')->unique()->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->nullable();
            $table->uuid('user_uuid')->unique();
            $table->string('seller_id')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

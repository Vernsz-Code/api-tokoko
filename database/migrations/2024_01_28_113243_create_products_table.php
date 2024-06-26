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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("description");
            $table->string("thumbnail");
            $table->unsignedBigInteger("store_id");
            $table->decimal("price");
            $table->integer("stock");
            $table->boolean("status");
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
            
            $table->foreign("category_id")->references("id")->on("categories");
            $table->foreign("store_id")->references("id")->on("stores");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

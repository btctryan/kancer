<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 0);   // harga domba bisa jutaan
            $table->string('image')->nullable();
            $table->string('badge')->nullable(); // Terlaris, Baru, Diskon, dll
            $table->integer('stock')->default(0);
            $table->decimal('weight', 5, 1)->nullable(); // berat dalam kg
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
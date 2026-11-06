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
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->string('name', 150);
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->decimal('old_price', 12, 2)->default(0);
            $table->decimal('new_price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('restrict');

            $table->foreign('subcategory_id')
                  ->references('id')
                  ->on('subcategories')
                  ->onDelete('restrict');

            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

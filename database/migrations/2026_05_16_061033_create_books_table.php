<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('restrict');
            $table->string('title', 255);
            $table->string('author', 150);
            $table->string('publisher', 150)->nullable();
            $table->string('isbn', 20)->unique()->nullable();
            $table->year('published_year')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('stock')->default(1);
            $table->string('cover_image', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
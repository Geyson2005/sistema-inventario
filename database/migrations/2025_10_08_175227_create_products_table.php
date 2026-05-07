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
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->text('description')->nullable();
            
            // Relaciones
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            
            // Control de stock
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(0);
            
            // Estado
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps();
            
            // Índices para búsqueda rápida
            $table->index('code');
            $table->index('name');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
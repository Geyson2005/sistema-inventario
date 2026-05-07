<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            $table->integer('quantity');
            $table->enum('type', ['increase', 'decrease']);
            $table->string('reason', 150);
            $table->date('adjustment_date');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('adjustment_date');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
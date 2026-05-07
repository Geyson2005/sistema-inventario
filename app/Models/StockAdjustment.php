<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'type',
        'reason',
        'adjustment_date',
        'notes',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'quantity' => 'integer',
    ];

    // Relaciones
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::created(function ($adjustment) {
            $product = $adjustment->product;
            $previousStock = $product->current_stock;
            
            // Actualizar stock según el tipo de ajuste
            if ($adjustment->type === 'increase') {
                $product->increment('current_stock', $adjustment->quantity);
            } else {
                $product->decrement('current_stock', $adjustment->quantity);
            }
            
            // Registrar en historial
            StockMovement::create([
                'product_id' => $adjustment->product_id,
                'user_id' => $adjustment->user_id,
                'movement_type' => 'adjustment',
                'quantity' => $adjustment->quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $product->fresh()->current_stock,
                'reference_id' => $adjustment->id,
                'reference_type' => self::class,
                'notes' => 'Ajuste: ' . $adjustment->reason,
            ]);
        });
    }

    // Etiquetas legibles
    public static function getTypeLabels()
    {
        return [
            'increase' => 'Incremento',
            'decrease' => 'Decremento',
        ];
    }

    public function getTypeLabelAttribute()
    {
        $labels = self::getTypeLabels();
        return $labels[$this->type] ?? $this->type;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'movement_type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reference_id',
        'reference_type',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
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

    // Relación polimórfica al registro original
    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes para filtros
    public function scopeEntries($query)
    {
        return $query->where('movement_type', 'entry');
    }

    public function scopeExits($query)
    {
        return $query->where('movement_type', 'exit');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('movement_type', 'adjustment');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Etiquetas legibles
    public static function getTypeLabels()
    {
        return [
            'entry' => 'Entrada',
            'exit' => 'Salida',
            'adjustment' => 'Ajuste',
        ];
    }

    public function getTypeLabelAttribute()
    {
        $labels = self::getTypeLabels();
        return $labels[$this->movement_type] ?? $this->movement_type;
    }

    public function getTypeColorAttribute()
    {
        return match($this->movement_type) {
            'entry' => 'success',
            'exit' => 'danger',
            'adjustment' => 'warning',
            default => 'secondary',
        };
    }
}
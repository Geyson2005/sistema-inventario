<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'unit_id',
        'current_stock',
        'min_stock',
        'status',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'min_stock' => 'integer',
    ];

    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function stockExits()
    {
        return $this->hasMany(StockExit::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->orderBy('created_at', 'desc');
    }

    // Scopes para consultas (RF09, RF10, RF16)
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock')
                     ->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('code', 'like', "%{$term}%")
              ->orWhere('name', 'like', "%{$term}%");
        });
    }

    // Accessors (propiedades calculadas)
    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->min_stock && $this->current_stock > 0;
    }

    public function getIsOutOfStockAttribute()
    {
        return $this->current_stock == 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->is_out_of_stock) {
            return 'out_of_stock';
        } elseif ($this->is_low_stock) {
            return 'low_stock';
        } else {
            return 'normal';
        }
    }

    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'Agotado',
            'low_stock' => 'Stock Bajo',
            'normal' => 'Normal',
        };
    }

    public function getStockStatusColorAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'normal' => 'success',
        };
    }

    // Verificar si tiene suficiente stock
    public function hasStock($quantity)
    {
        return $this->current_stock >= $quantity;
    }

    // Verificar si se puede eliminar (RF15)
    public function canDelete()
    {
        return $this->stockMovements()->count() === 0;
    }
}
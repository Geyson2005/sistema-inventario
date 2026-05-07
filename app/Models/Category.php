<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    // Relaciones
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? 'Activo' : 'Inactivo';
    }

    public function getStatusColorAttribute()
    {
        return $this->status === 'active' ? 'success' : 'secondary';
    }

    // Métodos auxiliares
    public function canDelete()
    {
        return $this->products()->count() === 0;
    }
}
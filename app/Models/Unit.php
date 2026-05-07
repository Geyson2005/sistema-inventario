<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
    ];

    // Relaciones
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
            ->orWhere('abbreviation', 'like', "%{$term}%");
        });
    }

    public function scopeOrderByName($query)
    {
        return $query->orderBy('name', 'asc');
    }

    // Métodos auxiliares
    public function canDelete()
    {
        return $this->products()->count() === 0;
    }

    // Accessor para mostrar nombre completo con abreviatura
    public function getFullNameAttribute()
    {
        return "{$this->name} ({$this->abbreviation})";
    }
}
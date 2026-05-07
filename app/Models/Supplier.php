<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ruc',
        'contact_person',
        'phone',
        'email',
        'address',
        'status',
    ];

    // Relación: Un proveedor tiene muchas entradas
    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    // Scope: Solo proveedores activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Verificar si se puede eliminar
    public function canDelete()
    {
        return $this->stockEntries()->count() === 0;
    }

    // Accessor: Nombre completo con RUC
    public function getFullNameAttribute()
    {
        if ($this->ruc) {
            return "{$this->name} - RUC: {$this->ruc}";
        }
        return $this->name;
    }
}
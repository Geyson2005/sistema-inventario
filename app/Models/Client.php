<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'document_type',
        'document_number',
        'phone',
        'email',
        'address',
        'status',
    ];

    // Relaciones
    public function stockExits()
    {
        return $this->hasMany(StockExit::class);
    }

    public function stockExitBatches()
    {
        return $this->hasMany(StockExitBatch::class);
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
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('document_number', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
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

    public function getDocumentTypeLabelAttribute()
    {
        return match($this->document_type) {
            'DNI' => 'DNI',
            'RUC' => 'RUC',
            'CE' => 'Carnet de Extranjería',
            'Pasaporte' => 'Pasaporte',
            'Otro' => 'Otro',
            default => '-',
        };
    }

    public function getFormattedDocumentAttribute()
    {
        if (!$this->document_number) {
            return 'Sin documento';
        }
        
        // Formatear RUC: 20-123456789
        if ($this->document_type === 'RUC' && strlen($this->document_number) === 11) {
            return substr($this->document_number, 0, 2) . '-' . substr($this->document_number, 2);
        }
        
        return $this->document_number;
    }

    public function getContactInfoAttribute()
    {
        $info = [];
        
        if ($this->phone) {
            $info[] = '📞 ' . $this->phone;
        }
        
        if ($this->email) {
            $info[] = '✉️ ' . $this->email;
        }
        
        return !empty($info) ? implode(' | ', $info) : 'Sin información de contacto';
    }

    // Métodos auxiliares
    public function canDelete()
    {
        return $this->stockExits()->count() === 0 && $this->stockExitBatches()->count() === 0;
    }

    public function getTotalPurchases()
    {
        return $this->stockExitBatches()->count();
    }

    public function getTotalQuantityPurchased()
    {
        return $this->stockExits()->sum('quantity');
    }
}
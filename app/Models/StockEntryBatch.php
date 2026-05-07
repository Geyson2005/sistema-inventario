<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'entry_date',
        'document_type',
        'document_number',
        'notes',
        'total_items',
        'total_quantity',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_items' => 'integer',
        'total_quantity' => 'integer',
    ];

    // Relaciones
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->hasMany(StockEntry::class, 'batch_id');
    }

    // Etiquetas legibles para tipos de documento
    public static function getDocumentTypeLabels()
    {
        return [
            'factura' => 'Factura',
            'boleta' => 'Boleta',
            'nota_entrada' => 'Nota de Entrada',
            'nota_salida' => 'Nota de Salida',
            'guia_remision' => 'Guía de Remisión',
            'otros' => 'Otros',
        ];
    }

    public function getDocumentTypeLabelAttribute()
    {
        if (!$this->document_type) {
            return 'Sin documento';
        }
        $labels = self::getDocumentTypeLabels();
        return $labels[$this->document_type] ?? 'Sin documento';
    }

    // Accessor: Documento completo
    public function getFullDocumentAttribute()
    {
        if ($this->document_type && $this->document_number) {
            return $this->document_type_label . ' N° ' . $this->document_number;
        }
        return 'Sin documento';
    }

    // Calcular totales
    public function calculateTotals()
    {
        $this->total_items = $this->entries()->count();
        $this->total_quantity = $this->entries()->sum('quantity');
        $this->save();
    }
}
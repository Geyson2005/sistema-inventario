<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',           // ⭐ AÑADIDO
        'product_id',
        'supplier_id',
        'user_id',
        'quantity',
        'entry_date',
        'document_type',
        'document_number',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'quantity' => 'integer',
    ];

    // Relaciones
    public function batch()                           // ⭐ AÑADIDO
    {
        return $this->belongsTo(StockEntryBatch::class, 'batch_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::created(function ($entry) {
            $product = $entry->product;
            $previousStock = $product->current_stock;
            
            // RF03: Actualizar automáticamente el stock
            $product->increment('current_stock', $entry->quantity);
            
            // Registrar en historial de movimientos
            StockMovement::create([
                'product_id' => $entry->product_id,
                'user_id' => $entry->user_id,
                'movement_type' => 'entry',
                'quantity' => $entry->quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $product->fresh()->current_stock,
                'reference_id' => $entry->id,
                'reference_type' => self::class,
                'notes' => $entry->supplier 
                    ? 'Entrada de proveedor: ' . $entry->supplier->name 
                    : 'Entrada de productos',
            ]);

            // ⭐ AÑADIDO: Actualizar totales del batch si existe
            if ($entry->batch_id) {
                $entry->batch->calculateTotals();
            }
        });
    }

    // ... resto del código sin cambios
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
        $labels = self::getDocumentTypeLabels();
        return $labels[$this->document_type] ?? 'Sin documento';
    }

    public function getFullDocumentAttribute()
    {
        if ($this->document_type && $this->document_number) {
            return $this->document_type_label . ' N° ' . $this->document_number;
        }
        return 'Sin documento';
    }
}
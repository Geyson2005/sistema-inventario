<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',           // ⭐ AÑADIDO
        'product_id',
        'user_id',
        'quantity',
        'exit_date',
        'reason',
        'document_type',
        'document_number',
        'notes',
    ];

    protected $casts = [
        'exit_date' => 'date',
        'quantity' => 'integer',
    ];

    // Relaciones
    public function batch()                           // ⭐ AÑADIDO
    {
        return $this->belongsTo(StockExitBatch::class, 'batch_id');
    }

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
        static::created(function ($exit) {
            $product = $exit->product;
            $previousStock = $product->current_stock;
            
            // RF07: Actualizar automáticamente el stock
            $product->decrement('current_stock', $exit->quantity);
            
            // Registrar en historial
            StockMovement::create([
                'product_id' => $exit->product_id,
                'user_id' => $exit->user_id,
                'movement_type' => 'exit',
                'quantity' => $exit->quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $product->fresh()->current_stock,
                'reference_id' => $exit->id,
                'reference_type' => self::class,
                'notes' => 'Salida por: ' . $exit->reason_label,
            ]);

            // ⭐ AÑADIDO: Actualizar totales del batch si existe
            if ($exit->batch_id) {
                $exit->batch->calculateTotals();
            }
        });
    }

    // ... resto del código sin cambios
    public static function getReasonLabels()
    {
        return [
            'sale' => 'Venta',
            'transfer' => 'Transferencia',
            'damage' => 'Daño/Merma',
            'other' => 'Otro',
        ];
    }

    public function getReasonLabelAttribute()
    {
        $labels = self::getReasonLabels();
        return $labels[$this->reason] ?? $this->reason;
    }

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

    public function getFullDocumentAttribute()
    {
        if ($this->document_type && $this->document_number) {
            return $this->document_type_label . ' N° ' . $this->document_number;
        }
        return 'Sin documento';
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
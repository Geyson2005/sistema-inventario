<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockExitBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id', 
        'exit_date',
        'reason',
        'document_type',
        'document_number',
        'notes',
        'total_items',
        'total_quantity',
    ];

    protected $casts = [
        'exit_date' => 'date',
        'total_items' => 'integer',
        'total_quantity' => 'integer',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exits()
    {
        return $this->hasMany(StockExit::class, 'batch_id');
    }

    // Etiquetas legibles para motivos
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
        $this->total_items = $this->exits()->count();
        $this->total_quantity = $this->exits()->sum('quantity');
        $this->save();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
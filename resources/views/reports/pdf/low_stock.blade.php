<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #dc3545; }
        .alert { background: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin-bottom: 20px; }
        .info { margin-bottom: 20px; font-size: 10px; }
        .summary { background: #f8f9fa; padding: 10px; margin-bottom: 20px; border: 1px solid #dee2e6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #343a40; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 6px; border-bottom: 1px solid #dee2e6; font-size: 10px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚠️ {{ $title }}</h1>
        <p>Sistema de Almacén</p>
    </div>

    <div class="alert">
        <strong>ALERTA:</strong> Este reporte muestra productos que requieren reposición inmediata.
    </div>

    <div class="info">
        <strong>Generado:</strong> {{ $generated_at->format('d/m/Y H:i:s') }} | <strong>Por:</strong> {{ $generated_by }}
    </div>

    <div class="summary">
        <strong>RESUMEN:</strong><br>
        Total Productos Críticos: <strong>{{ $summary['total_products'] }}</strong> |
        Agotados: <strong style="color: #dc3545;">{{ $summary['out_of_stock'] }}</strong> |
        Stock Bajo: <strong style="color: #ffc107;">{{ $summary['low_stock'] }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">Código</th>
                <th width="35%">Producto</th>
                <th width="15%">Categoría</th>
                <th width="12%" class="text-center">Stock Actual</th>
                <th width="12%" class="text-center">Stock Mín.</th>
                <th width="16%" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td><strong>{{ $product->name }}</strong></td>
                <td>{{ $product->category->name }}</td>
                <td class="text-center">
                    <strong style="color: {{ $product->current_stock == 0 ? '#dc3545' : '#ffc107' }};">
                        {{ $product->current_stock }}
                    </strong> {{ $product->unit->abbreviation }}
                </td>
                <td class="text-center">{{ $product->min_stock }} {{ $product->unit->abbreviation }}</td>
                <td class="text-center">
                    @if($product->is_out_of_stock)
                        <span class="badge badge-danger">AGOTADO</span>
                    @else
                        <span class="badge badge-warning">STOCK BAJO</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Página <span class="pagenum"></span> - Sistema de Almacén</div>
</body>
</html>
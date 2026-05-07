<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .info {
            margin-bottom: 20px;
            font-size: 10px;
        }
        .summary {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #343a40;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 6px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; color: white; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Sistema de Almacén</p>
    </div>

    <!-- Información de Generación -->
    <div class="info">
        <strong>Generado:</strong> {{ $generated_at->format('d/m/Y H:i:s') }} | 
        <strong>Por:</strong> {{ $generated_by }}
        @if(isset($filters['category_id']) && $filters['category_id'])
            | <strong>Categoría:</strong> {{ \App\Models\Category::find($filters['category_id'])->name }}
        @endif
    </div>

    <!-- Resumen -->
    <div class="summary">
        <strong>RESUMEN:</strong><br>
        <div class="summary-item">Total Productos: <strong>{{ $summary['total_products'] }}</strong></div>
        <div class="summary-item">Con Stock: <strong>{{ $summary['products_with_stock'] }}</strong></div>
        <div class="summary-item">Stock Bajo: <strong>{{ $summary['low_stock'] }}</strong></div>
        <div class="summary-item">Agotados: <strong>{{ $summary['out_of_stock'] }}</strong></div>
    </div>

    <!-- Tabla de Productos -->
    <table>
        <thead>
            <tr>
                <th width="8%">Código</th>
                <th width="32%">Producto</th>
                <th width="15%">Categoría</th>
                <th width="10%" class="text-center">Stock</th>
                <th width="10%" class="text-center">Mín.</th>
                <th width="15%" class="text-center">Estado</th>
                <th width="10%">Estado Prod.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td class="text-center">
                    <strong>{{ $product->current_stock }}</strong> {{ $product->unit->abbreviation }}
                </td>
                <td class="text-center">{{ $product->min_stock }}</td>
                <td class="text-center">
                    @if($product->is_out_of_stock)
                        <span class="badge badge-danger">AGOTADO</span>
                    @elseif($product->is_low_stock)
                        <span class="badge badge-warning">BAJO</span>
                    @else
                        <span class="badge badge-success">NORMAL</span>
                    @endif
                </td>
                <td>{{ $product->status === 'active' ? 'Activo' : 'Inactivo' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Página <span class="pagenum"></span> - Sistema de Almacén
    </div>
</body>
</html>
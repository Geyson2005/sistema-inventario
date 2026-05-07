<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            font-size: 10px;
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
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
        .category-detail {
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
    </div>

    <!-- Resumen -->
    <div class="summary">
        <strong>RESUMEN GENERAL:</strong><br>
        Total Categorías: <strong>{{ $summary['total_categories'] }}</strong> | 
        Total Productos: <strong>{{ $summary['total_products'] }}</strong>
    </div>

    <!-- Tabla de Categorías -->
    <table>
        <thead>
            <tr>
                <th width="5%">N°</th>
                <th width="25%">Categoría</th>
                <th width="15%" class="text-center">Productos</th>
                <th width="15%" class="text-center">Stock Total</th>
                <th width="13%" class="text-center">Stock Bajo</th>
                <th width="13%" class="text-center">Agotados</th>
                <th width="14%" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $index => $item)
                @php 
                    $category = $item['category'];
                    $percentage = $item['total_products'] > 0 
                        ? round(($item['total_products'] / $summary['total_products']) * 100, 1) 
                        : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $category->name }}</strong>
                        @if($category->description)
                            <div class="category-detail">{{ Str::limit($category->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <strong>{{ $item['total_products'] }}</strong>
                        <span class="category-detail">({{ $percentage }}%)</span>
                    </td>
                    <td class="text-center">
                        <strong>{{ number_format($item['total_stock']) }}</strong>
                    </td>
                    <td class="text-center">
                        @if($item['low_stock'] > 0)
                            <span class="badge badge-warning">{{ $item['low_stock'] }}</span>
                        @else
                            <span style="color: #28a745;">✓</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item['out_of_stock'] > 0)
                            <span class="badge badge-danger">{{ $item['out_of_stock'] }}</span>
                        @else
                            <span style="color: #28a745;">✓</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item['out_of_stock'] > 0)
                            <span class="badge badge-danger">CRÍTICO</span>
                        @elseif($item['low_stock'] > 0)
                            <span class="badge badge-warning">ALERTA</span>
                        @else
                            <span class="badge badge-success">NORMAL</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #e9ecef; font-weight: bold;">
                <td colspan="2" class="text-center">TOTALES</td>
                <td class="text-center">{{ $categories->sum('total_products') }}</td>
                <td class="text-center">{{ number_format($categories->sum('total_stock')) }}</td>
                <td class="text-center">{{ $categories->sum('low_stock') }}</td>
                <td class="text-center">{{ $categories->sum('out_of_stock') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if($categories->count() > 0)
        <!-- Análisis por Categoría -->
        <div style="background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; margin-top: 20px;">
            <strong>ANÁLISIS POR CATEGORÍA:</strong><br>
            <div style="margin-top: 5px;">
                @php
                    $topCategory = $categories->sortByDesc('total_products')->first();
                    $criticalCategories = $categories->filter(fn($c) => $c['out_of_stock'] > 0)->count();
                    $warningCategories = $categories->filter(fn($c) => $c['low_stock'] > 0 && $c['out_of_stock'] == 0)->count();
                    $healthyCategories = $categories->filter(fn($c) => $c['low_stock'] == 0 && $c['out_of_stock'] == 0)->count();
                @endphp
                • Categoría con más productos: <strong>{{ $topCategory['category']->name }}</strong> ({{ $topCategory['total_products'] }} productos)<br>
                • Categorías en estado crítico: <strong style="color: #dc3545;">{{ $criticalCategories }}</strong><br>
                • Categorías en alerta: <strong style="color: #ffc107;">{{ $warningCategories }}</strong><br>
                • Categorías en buen estado: <strong style="color: #28a745;">{{ $healthyCategories }}</strong><br>
                • Promedio de productos por categoría: <strong>{{ number_format($categories->avg('total_products'), 2) }}</strong>
            </div>
        </div>

        <!-- Top 3 Categorías -->
        @php
            $top3 = $categories->sortByDesc('total_products')->take(3);
        @endphp
        @if($top3->count() > 0)
            <div style="background: #e7f3ff; padding: 10px; border: 1px solid #007bff; margin-top: 15px;">
                <strong>TOP 3 CATEGORÍAS MÁS IMPORTANTES:</strong><br>
                <ol style="margin: 5px 0 0 15px; padding: 0;">
                    @foreach($top3 as $item)
                        <li>
                            <strong>{{ $item['category']->name }}</strong> - 
                            {{ $item['total_products'] }} productos, 
                            Stock total: {{ number_format($item['total_stock']) }} unidades
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        Página <span class="pagenum"></span> - Sistema de Almacén - Reporte generado automáticamente
    </div>
</body>
</html>
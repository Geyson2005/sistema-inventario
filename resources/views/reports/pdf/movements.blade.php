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
            padding: 5px;
            border-bottom: 1px solid #dee2e6;
            font-size: 9px;
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
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
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
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Sistema de Almacén</p>
    </div>

    <div class="info">
        <strong>Generado:</strong> {{ $generated_at->format('d/m/Y H:i:s') }} | 
        <strong>Por:</strong> {{ $generated_by }}
        @if(isset($filters['date_from']) && $filters['date_from'])
            | <strong>Periodo:</strong> {{ $filters['date_from'] }} al {{ $filters['date_to'] }}
        @endif
    </div>

    <div class="summary">
        <strong>RESUMEN:</strong><br>
        Total Movimientos: <strong>{{ $summary['total_movements'] }}</strong> |
        Entradas: <strong>{{ $summary['total_entries'] }}</strong> ({{ number_format($summary['quantity_in']) }} unidades) |
        Salidas: <strong>{{ $summary['total_exits'] }}</strong> ({{ number_format($summary['quantity_out']) }} unidades) |
        Ajustes: <strong>{{ $summary['total_adjustments'] }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Fecha</th>
                <th width="10%">Tipo</th>
                <th width="30%">Producto</th>
                <th width="10%" class="text-center">Cantidad</th>
                <th width="10%" class="text-center">Stock Ant.</th>
                <th width="10%" class="text-center">Stock Nuevo</th>
                <th width="18%">Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
            <tr>
                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($movement->movement_type === 'entry')
                        <span class="badge badge-success">ENTRADA</span>
                    @elseif($movement->movement_type === 'exit')
                        <span class="badge badge-danger">SALIDA</span>
                    @else
                        <span class="badge badge-warning">AJUSTE</span>
                    @endif
                </td>
                <td>{{ $movement->product->name }}</td>
                <td class="text-center">
                    @if($movement->movement_type === 'entry')
                        +{{ $movement->quantity }}
                    @else
                        -{{ $movement->quantity }}
                    @endif
                    {{ $movement->product->unit->abbreviation }}
                </td>
                <td class="text-center">{{ $movement->previous_stock }}</td>
                <td class="text-center"><strong>{{ $movement->new_stock }}</strong></td>
                <td>{{ $movement->user->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Página <span class="pagenum"></span> - Sistema de Almacén
    </div>
</body>
</html>
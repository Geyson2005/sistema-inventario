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
            font-size: 8px;
            font-weight: bold;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .badge-primary {
            background: #007bff;
            color: white;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-secondary {
            background: #6c757d;
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
        @if(isset($filters['date_from']) && $filters['date_from'])
            | <strong>Periodo:</strong> {{ $filters['date_from'] }} al {{ $filters['date_to'] }}
        @endif
    </div>

    <!-- Resumen -->
    <div class="summary">
        <strong>RESUMEN:</strong><br>
        Total Usuarios: <strong>{{ $summary['total_users'] }}</strong> | 
        Total Movimientos Registrados: <strong>{{ $summary['total_movements'] }}</strong>
    </div>

    <!-- Tabla de Usuarios -->
    <table>
        <thead>
            <tr>
                <th width="25%">Usuario</th>
                <th width="12%">Email</th>
                <th width="10%">Rol</th>
                <th width="8%">Estado</th>
                <th width="11%" class="text-center">Entradas</th>
                <th width="11%" class="text-center">Salidas</th>
                <th width="11%" class="text-center">Ajustes</th>
                <th width="12%" class="text-center">Total Movs.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $item)
                @php $user = $item['user']; @endphp
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td style="font-size: 9px;">{{ Str::limit($user->email, 20) }}</td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="badge badge-danger">Administrador</span>
                        @else
                            <span class="badge badge-primary">Operador</span>
                        @endif
                    </td>
                    <td>
                        @if($user->status === 'active')
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item['entries'] }}</td>
                    <td class="text-center">{{ $item['exits'] }}</td>
                    <td class="text-center">{{ $item['adjustments'] }}</td>
                    <td class="text-center"><strong>{{ $item['total_movements'] }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales Generales -->
    <div style="background: #e9ecef; padding: 10px; margin-top: 20px; border: 1px solid #dee2e6;">
        <strong>TOTALES GENERALES:</strong><br>
        @php
            $totalEntries = collect($users)->sum('entries');
            $totalExits = collect($users)->sum('exits');
            $totalAdjustments = collect($users)->sum('adjustments');
            $totalAll = collect($users)->sum('total_movements');
        @endphp
        Total Entradas: <strong>{{ $totalEntries }}</strong> | 
        Total Salidas: <strong>{{ $totalExits }}</strong> | 
        Total Ajustes: <strong>{{ $totalAdjustments }}</strong> | 
        <strong>TOTAL GENERAL: {{ $totalAll }}</strong>
    </div>

    <!-- Footer -->
    <div class="footer">
        Página <span class="pagenum"></span> - Sistema de Almacén
    </div>
</body>
</html>
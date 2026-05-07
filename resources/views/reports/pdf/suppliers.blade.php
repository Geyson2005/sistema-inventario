<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .info { margin-bottom: 20px; font-size: 10px; }
        .summary { background: #f8f9fa; padding: 10px; margin-bottom: 20px; border: 1px solid #dee2e6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #343a40; color: white; padding: 8px; text-align: left; font-size: 10px; }
        td { padding: 6px; border-bottom: 1px solid #dee2e6; font-size: 10px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Sistema de Almacén</p>
    </div>

    <div class="info">
        <strong>Generado:</strong> {{ $generated_at->format('d/m/Y H:i:s') }} | <strong>Por:</strong> {{ $generated_by }}
    </div>

    <div class="summary">
        <strong>RESUMEN:</strong><br>
        Total Proveedores: <strong>{{ $summary['total_suppliers'] }}</strong> |
        Activos: <strong>{{ $summary['active_suppliers'] }}</strong> |
        Total Entradas: <strong>{{ $summary['total_entries'] }}</strong> |
        Cantidad Total: <strong>{{ number_format($summary['total_quantity']) }} unidades</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th width="25%">Nombre</th>
                <th width="12%">RUC</th>
                <th width="20%">Contacto</th>
                <th width="18%">Teléfono/Email</th>
                <th width="10%" class="text-center">Entradas</th>
                <th width="10%" class="text-center">Cantidad</th>
                <th width="5%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $item)
                @php $supplier = $item['supplier']; @endphp
                <tr>
                    <td><strong>{{ $supplier->name }}</strong></td>
                    <td>{{ $supplier->formatted_ruc }}</td>
                    <td>{{ $supplier->contact_person ?? '-' }}</td>
                    <td style="font-size: 9px;">
                        {{ $supplier->phone ?? '-' }}<br>
                        {{ $supplier->email ?? '' }}
                    </td>
                    <td class="text-center"><strong>{{ $item['total_entries'] }}</strong></td>
                    <td class="text-center">{{ number_format($item['total_quantity']) }}</td>
                    <td>
                        @if($supplier->status === 'active')
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-secondary">Inactivo</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Página <span class="pagenum"></span> - Sistema de Almacén</div>
</body>
</html>
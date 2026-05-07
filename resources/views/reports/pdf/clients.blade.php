<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #343a40; color: white; padding: 8px; text-align: left; font-size: 10px; }
        td { padding: 6px; border-bottom: 1px solid #dee2e6; font-size: 10px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Sistema de Almacén</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Tipo Doc.</th>
                <th>N° Documento</th>
                <th class="text-center">Productos</th>
                <th class="text-center">Cantidad</th>
            </tr>
            </thead>
        <tbody>
            @foreach($data['batches'] as $item)
            <tr>
                <td>
                    <strong>{{ optional($item->client)->name ?? 'Sin cliente' }}</strong><br>
                    <small>{{ optional($item->client)->code ?? '-' }}</small>
                </td>
                <td>{{ \Carbon\Carbon::parse($item->exit_date)->format('d/m/Y') }}</td>
                <td>{{ ucfirst($item->document_type ?? '-') }}</td>
                <td>{{ $item->document_number ?? '-' }}</td>
                <td class="text-center">{{ $item->exits->count() }}</td>
                <td class="text-center">{{ number_format($item->total_quantity) }}</td>
            </tr>
            @endforeach
            </tbody>
    </table>

    <div class="footer">Página <span class="pagenum"></span> - Sistema de Almacén</div>
</body>
</html>
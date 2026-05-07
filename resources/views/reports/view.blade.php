@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- TÍTULO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-file-alt text-primary"></i>
            {{ $data['title'] ?? 'Reporte' }}
        </h2>

        <div>
            {{-- EXPORTAR PDF --}}
            <form action="{{ route('reports.generate') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="report_type" value="{{ $reportType }}">
                <input type="hidden" name="format" value="pdf">

                {{-- reenviar filtros desde request() si existen --}}
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                <input type="hidden" name="supplier_id" value="{{ request('supplier_id') }}">
                <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                <input type="hidden" name="movement_type" value="{{ request('movement_type') }}">

                <button class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
            </form>

            {{-- EXPORTAR EXCEL --}}
            <form action="{{ route('reports.generate') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="report_type" value="{{ $reportType }}">
                <input type="hidden" name="format" value="excel">

                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                <input type="hidden" name="supplier_id" value="{{ request('supplier_id') }}">
                <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                <input type="hidden" name="movement_type" value="{{ request('movement_type') }}">

                <button class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
            </form>
        </div>
    </div>

    {{-- INFORMACIÓN DEL REPORTE (protegida) --}}
    <div class="alert alert-info">
        <strong>Generado por:</strong> {{ $data['generated_by'] ?? auth()->user()->name ?? 'Sistema' }} <br>
        <strong>Fecha generación:</strong> {{ $data['generated_at'] ?? now() }}
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- ========== INVENTARIO ========== --}}
            @if($reportType === 'inventory')
                @php $items = $data['products'] ?? collect(); @endphp

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Stock Actual</th>
                            <th>Mínimo</th>
                            <th>Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $p)
                            <tr>
                                <td>{{ $p->name ?? ($p['name'] ?? '—') }}</td>
                                <td>{{ $p->category->name ?? ($p['category']['name'] ?? '—') }}</td>
                                <td>{{ $p->current_stock ?? ($p['current_stock'] ?? '0') }}</td>
                                <td>{{ $p->min_stock ?? ($p['min_stock'] ?? ($p->minimum_stock ?? '0')) }}</td>
                                <td>{{ $p->unit->abbreviation ?? ($p['unit']['abbreviation'] ?? '-') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No hay productos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- ========== MOVIMIENTOS ========== --}}
            @if($reportType === 'movements')
                @php $items = $data['movements'] ?? collect(); @endphp

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $m)
                            <tr>
                                <td>{{ $m->created_at ?? ($m['created_at'] ?? '—') }}</td>
                                <td>{{ $m->user->name ?? ($m['user']['name'] ?? '—') }}</td>
                                <td>{{ $m->product->name ?? ($m['product']['name'] ?? ($m['product'] ?? '—')) }}</td>
                                <td>{{ $m->quantity ?? ($m['quantity'] ?? '0') }}</td>
                                <td>{{ $m->movement_type ?? ($m['movement_type'] ?? ($m['type'] ?? '—')) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No hay movimientos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- ========== PROVEEDORES ========== --}}
            @if($reportType === 'suppliers')
                @php $items = $data['suppliers'] ?? collect(); @endphp

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Proveedor</th>
                            <th>Total Entradas</th>
                            <th>Total Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $s)
                            <tr>
                                {{-- tu suppliersData devuelve arrays con keys: supplier, total_entries, total_quantity --}}
                                <td>{{ $s['supplier']->name ?? ($s['supplier']['name'] ?? '—') }}</td>
                                <td>{{ $s['total_entries'] ?? 0 }}</td>
                                <td>{{ $s['total_quantity'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">No hay proveedores.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- ========== BAJO STOCK ========== --}}
            @if($reportType === 'low_stock')
                @php $items = $data['products'] ?? collect(); @endphp

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Mínimo</th>
                            <th>Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $p)
                            <tr>
                                <td>{{ $p->name ?? ($p['name'] ?? '—') }}</td>
                                <td>{{ $p->current_stock ?? ($p['current_stock'] ?? '0') }}</td>
                                <td>{{ $p->min_stock ?? ($p['min_stock'] ?? ($p->minimum_stock ?? '0')) }}</td>
                                <td>{{ $p->unit->abbreviation ?? ($p['unit']['abbreviation'] ?? '-') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">No hay productos con stock bajo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- ========== CATEGORÍAS ========== --}}
            @if($reportType === 'categories')
                @php $items = $data['categories'] ?? collect(); @endphp

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Categoría</th>
                            <th>Total Productos</th>
                            <th>Total Stock</th>
                            <th>Bajo stock</th>
                            <th>Agotados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $c)
                            <tr>
                                <td>{{ $c['category']->name ?? ($c['category']['name'] ?? '—') }}</td>
                                <td>{{ $c['total_products'] ?? 0 }}</td>
                                <td>{{ $c['total_stock'] ?? 0 }}</td>
                                <td>{{ $c['low_stock'] ?? 0 }}</td>
                                <td>{{ $c['out_of_stock'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No hay categorías.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- ========== ACTIVIDAD DE USUARIOS ========== --}}
            @if($reportType === 'users_activity')
                @php $items = $data['users'] ?? collect(); @endphp

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Entradas</th>
                            <th>Salidas</th>
                            <th>Ajustes</th>
                            <th>Total Movimientos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $u)
                            <tr>
                                <td>{{ $u['user']->name ?? ($u['user']['name'] ?? '—') }}</td>
                                <td>{{ $u['user']->email ?? ($u['user']['email'] ?? '—') }}</td>
                                <td>{{ $u['entries'] ?? 0 }}</td>
                                <td>{{ $u['exits'] ?? 0 }}</td>
                                <td>{{ $u['adjustments'] ?? 0 }}</td>
                                <td>{{ $u['total_movements'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No hay actividad de usuarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- ========== CLIENTES ========== --}}
            @if($reportType === 'clients')
                @php $items = $data['clients'] ?? collect(); @endphp
                <table class="table table-bordered">
                <thead class="table-primary">
                <tr>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Tipo Doc.</th>
                    <th>N° Documento</th>
                    <th>Total Productos</th>
                    <th>Total Cantidad</th>
                </tr>
                </thead>
                    <tbody>
                        @foreach($data['batches'] as $batch)
                        <tr>
                            <td>
                                <strong>{{ $batch->client->name ?? '---' }}</strong><br>
                                <small class="text-muted">
                                    {{ $batch->client->code ?? '' }}
                                </small>
                            </td>

                            <td>{{ \Carbon\Carbon::parse($batch->exit_date)->format('d/m/Y') }}</td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $batch->document_type }}
                                </span>
                            </td>

                            <td>{{ $batch->document_number }}</td>

                            <td>{{ $batch->exits->count() }}</td>

                            <td>{{ $batch->exits->sum('quantity') }}</td>
                        </tr>
                        @endforeach
                        </tbody>

                    <tfoot class="fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">TOTALES</td>
                            <td>{{ $data['summary']['total_purchases'] ?? 0 }}</td>
                            <td>{{ $data['summary']['total_quantity'] ?? 0 }}</td>
                        </tr>
                        </tfoot>
                </table>
            @endif

        </div>
    </div>

</div>
@endsection

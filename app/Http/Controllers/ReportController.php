<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\StockMovement;
use App\Models\StockEntry;
use App\Models\StockExit;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use App\Exports\MovementsExport;
use App\Exports\SuppliersExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Mostrar el panel principal de reportes
     */
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::lowStock()->count(),
            'out_of_stock_products' => Product::outOfStock()->count(),
            'total_entries' => StockEntry::count(),
            'total_exits' => StockExit::count(),
            'total_suppliers' => Supplier::active()->count(),
            'total_categories' => Category::active()->count(),
        ];

        // Movimientos del mes actual
        $currentMonth = Carbon::now()->startOfMonth();
        $monthlyStats = [
            'entries' => StockEntry::where('entry_date', '>=', $currentMonth)->count(),
            'exits' => StockExit::where('exit_date', '>=', $currentMonth)->count(),
            'adjustments' => StockMovement::where('movement_type', 'adjustment')
                ->where('created_at', '>=', $currentMonth)
                ->count(),
        ];

        $clients = Client::active()->orderBy('name')->get();

        return view('reports.index', compact('stats', 'monthlyStats','clients' ));
    }

    /**
     * Generar reporte según tipo y parámetros
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:inventory,movements,suppliers,low_stock,categories,users_activity,clients',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'client_id' => 'nullable|exists:clients,id',
            'movement_type' => 'nullable|in:entry,exit,adjustment',
            'format' => 'required|in:view,pdf,excel',
        ]);

        $reportType = $validated['report_type'];
        $format = $validated['format'];

        // Generar datos según tipo de reporte
        $data = $this->getReportData($reportType, $validated);
        $generated_by = auth()->user()->name ?? 'Sistema';


        // Devolver según formato
        switch ($format) {
            case 'pdf':
                return $this->generatePDF($reportType, $data);
            case 'excel':
                return $this->generateExcel($reportType, $data);
            default:
                return view('reports.view', compact('data', 'reportType', 'generated_by'));
        }
    }

    /**
     * Obtener datos según tipo de reporte
     */
    private function getReportData($type, $filters)
    {
        switch ($type) {
            case 'inventory':
                return $this->getInventoryReport($filters);
            case 'movements':
                return $this->getMovementsReport($filters);
            case 'suppliers':
                return $this->getSuppliersReport($filters);
            case 'clients':
            return $this->getClientsReport($filters);
            case 'low_stock':
                return $this->getLowStockReport($filters);
            case 'categories':
                return $this->getCategoriesReport($filters);
            case 'users_activity':
                return $this->getUsersActivityReport($filters);
            default:
                return [];
        }
    }

    /**
     * Reporte de Inventario Actual
     */
    private function getInventoryReport($filters)
    {
        $query = Product::with(['category', 'unit']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $products = $query->orderBy('name')->get();

        // Calcular totales
        $totalValue = 0;
        $productsWithStock = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;

        foreach ($products as $product) {
            if ($product->current_stock > 0) {
                $productsWithStock++;
            }
            if ($product->is_low_stock) {
                $lowStockCount++;
            }
            if ($product->is_out_of_stock) {
                $outOfStockCount++;
            }
        }

        return [
            'title' => 'Reporte de Inventario Actual',
            'products' => $products,
            'filters' => $filters,
            'summary' => [
                'total_products' => $products->count(),
                'products_with_stock' => $productsWithStock,
                'low_stock' => $lowStockCount,
                'out_of_stock' => $outOfStockCount,
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user()->name,
        ];
    }

    /**
     * Reporte de Movimientos de Stock
     */
    private function getMovementsReport($filters)
    {
        $query = StockMovement::with(['product.unit', 'user']);

        // Filtro por fechas
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filtro por tipo de movimiento
        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        // Calcular estadísticas
        $entradas = $movements->where('movement_type', 'entry')->sum('quantity');
        $salidas = $movements->where('movement_type', 'exit')->sum('quantity');

        return [
            'title' => 'Reporte de Movimientos de Stock',
            'movements' => $movements,
            'filters' => $filters,
            'summary' => [
                'total_movements' => $movements->count(),
                'total_entries' => $movements->where('movement_type', 'entry')->count(),
                'total_exits' => $movements->where('movement_type', 'exit')->count(),
                'total_adjustments' => $movements->where('movement_type', 'adjustment')->count(),
                'quantity_in' => $entradas,
                'quantity_out' => $salidas,
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user()->name,
        ];
    }

    /**
     * Reporte de Proveedores
     */
    private function getSuppliersReport($filters)
    {
        $suppliers = Supplier::with(['stockEntries' => function($query) use ($filters) {
            if (!empty($filters['date_from'])) {
                $query->whereDate('entry_date', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $query->whereDate('entry_date', '<=', $filters['date_to']);
            }
        }])->get();

        // Calcular estadísticas por proveedor
        $suppliersData = $suppliers->map(function($supplier) {
            return [
                'supplier' => $supplier,
                'total_entries' => $supplier->stockEntries->count(),
                'total_quantity' => $supplier->stockEntries->sum('quantity'),
            ];
        })->sortByDesc('total_entries');

        return [
            'title' => 'Reporte de Proveedores',
            'suppliers' => $suppliersData,
            'filters' => $filters,
            'summary' => [
                'total_suppliers' => $suppliers->count(),
                'active_suppliers' => $suppliers->where('status', 'active')->count(),
                'total_entries' => $suppliersData->sum('total_entries'),
                'total_quantity' => $suppliersData->sum('total_quantity'),
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user()->name,
        ];
    }

    /**
     * Reporte de Productos con Stock Bajo
     */
    private function getLowStockReport($filters)
    {
        $query = Product::with(['category', 'unit'])
            ->where(function($q) {
                $q->whereColumn('current_stock', '<=', 'min_stock')
                  ->orWhere('current_stock', 0);
            });

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $products = $query->orderBy('current_stock', 'asc')->get();

        return [
            'title' => 'Reporte de Stock Bajo y Agotado',
            'products' => $products,
            'filters' => $filters,
            'summary' => [
                'total_products' => $products->count(),
                'out_of_stock' => $products->where('current_stock', 0)->count(),
                'low_stock' => $products->where('current_stock', '>', 0)->count(),
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user()->name,
        ];
    }

    /**
     * Reporte por Categorías
     */
    private function getCategoriesReport($filters)
    {
        $categories = Category::with(['products.unit'])->get();

        $categoriesData = $categories->map(function($category) {
            $products = $category->products;
            return [
                'category' => $category,
                'total_products' => $products->count(),
                'total_stock' => $products->sum('current_stock'),
                'low_stock' => $products->filter(fn($p) => $p->is_low_stock)->count(),
                'out_of_stock' => $products->filter(fn($p) => $p->is_out_of_stock)->count(),
            ];
        });

        return [
            'title' => 'Reporte por Categorías',
            'categories' => $categoriesData,
            'filters' => $filters,
            'summary' => [
                'total_categories' => $categories->count(),
                'total_products' => $categoriesData->sum('total_products'),
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user()->name,
        ];
    }

    /**
     * Reporte de Actividad de Usuarios
     */
    private function getUsersActivityReport($filters)
    {
        $query = User::with(['stockEntries', 'stockExits', 'stockAdjustments']);

        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $query->with(['stockEntries' => function($q) use ($filters) {
                if (!empty($filters['date_from'])) {
                    $q->whereDate('entry_date', '>=', $filters['date_from']);
                }
                if (!empty($filters['date_to'])) {
                    $q->whereDate('entry_date', '<=', $filters['date_to']);
                }
            }]);
        }

        $users = $query->get();

        $usersData = $users->map(function($user) {
            return [
                'user' => $user,
                'entries' => $user->stockEntries->count(),
                'exits' => $user->stockExits->count(),
                'adjustments' => $user->stockAdjustments->count(),
                'total_movements' => $user->stockMovements->count(),
            ];
        })->sortByDesc('total_movements');

        return [
            'title' => 'Reporte de Actividad de Usuarios',
            'users' => $usersData,
            'filters' => $filters,
            'summary' => [
                'total_users' => $users->count(),
                'total_movements' => $usersData->sum('total_movements'),
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user()->name,
        ];
    }

    private function getClientsReport($filters)
        {
            $query = \App\Models\StockExitBatch::with([
                'client',
                'exits'
            ]);

            // Filtro por cliente
            if (!empty($filters['client_id'])) {
                $query->where('client_id', $filters['client_id']);
            }

            // Filtro por fechas
            if (!empty($filters['date_from'])) {
                $query->whereDate('exit_date', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('exit_date', '<=', $filters['date_to']);
            }

            $batches = $query->orderBy('exit_date', 'desc')->get();

            return [
                'title' => 'Reporte Detallado de Compras por Cliente',
                'batches' => $batches,
                'filters' => $filters,
                'summary' => [
                    'total_purchases' => $batches->count(),
                    'total_quantity' => $batches->sum(fn ($b) => $b->exits->sum('quantity')),
                ],
                'generated_at' => now(),
                'generated_by' => auth()->user()->name,
            ];
        }


    /**
     * Generar PDF
     */
    private function generatePDF($reportType, $data)
        {
            $pdf = PDF::loadView('reports.pdf.' . $reportType, [
                'data' => $data,                             // ✅ CLAVE
                'title' => $data['title'] ?? 'Reporte',
                'generated_by' => auth()->user()->name ?? 'Sistema'
            ]);

            $filename = $reportType . '_' . now()->format('Y-m-d_His') . '.pdf';
            return $pdf->download($filename);
        }

    /**
     * Generar Excel
     */
    private function generateExcel($reportType, $data)
    {
        $filename = $reportType . '_' . now()->format('Y-m-d_His') . '.xlsx';
        
        switch ($reportType) {
            case 'inventory':
                return Excel::download(new InventoryExport($data), $filename);
            case 'movements':
                return Excel::download(new MovementsExport($data), $filename);
            case 'suppliers':
                return Excel::download(new SuppliersExport($data), $filename);
            default:
                return Excel::download(new InventoryExport($data), $filename);
        }
    }
}
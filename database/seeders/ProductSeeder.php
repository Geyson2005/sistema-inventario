<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Electrónica
            [
                'code' => 'ELECT-001',
                'name' => 'Mouse Inalámbrico Logitech',
                'description' => 'Mouse inalámbrico con sensor óptico de alta precisión',
                'category_id' => 1,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 10,
                'status' => 'active',
            ],
            [
                'code' => 'ELECT-002',
                'name' => 'Teclado Mecánico RGB',
                'description' => 'Teclado mecánico con iluminación RGB personalizable',
                'category_id' => 1,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 5,
                'status' => 'active',
            ],
            [
                'code' => 'ELECT-003',
                'name' => 'Audífonos Bluetooth',
                'description' => 'Audífonos inalámbricos con cancelación de ruido',
                'category_id' => 1,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 8,
                'status' => 'active',
            ],

            // Alimentos
            [
                'code' => 'ALIM-001',
                'name' => 'Arroz Blanco',
                'description' => 'Arroz extra superior grado A',
                'category_id' => 2,
                'unit_id' => 2,
                'current_stock' => 0,
                'min_stock' => 50,
                'status' => 'active',
            ],
            [
                'code' => 'ALIM-002',
                'name' => 'Aceite Vegetal',
                'description' => 'Aceite vegetal 100% natural',
                'category_id' => 2,
                'unit_id' => 4,
                'current_stock' => 0,
                'min_stock' => 20,
                'status' => 'active',
            ],
            [
                'code' => 'ALIM-003',
                'name' => 'Azúcar Blanca',
                'description' => 'Azúcar blanca refinada',
                'category_id' => 2,
                'unit_id' => 2,
                'current_stock' => 0,
                'min_stock' => 30,
                'status' => 'active',
            ],

            // Limpieza
            [
                'code' => 'LIMP-001',
                'name' => 'Detergente en Polvo',
                'description' => 'Detergente en polvo concentrado',
                'category_id' => 3,
                'unit_id' => 2,
                'current_stock' => 0,
                'min_stock' => 15,
                'status' => 'active',
            ],
            [
                'code' => 'LIMP-002',
                'name' => 'Lejía',
                'description' => 'Lejía desinfectante cloro al 5%',
                'category_id' => 3,
                'unit_id' => 4,
                'current_stock' => 0,
                'min_stock' => 25,
                'status' => 'active',
            ],
            [
                'code' => 'LIMP-003',
                'name' => 'Escoba de Paja',
                'description' => 'Escoba de paja natural con mango de madera',
                'category_id' => 3,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 10,
                'status' => 'active',
            ],

            // Oficina
            [
                'code' => 'OFIC-001',
                'name' => 'Papel Bond A4',
                'description' => 'Papel bond blanco tamaño A4 - Paquete 500 hojas',
                'category_id' => 4,
                'unit_id' => 9,
                'current_stock' => 0,
                'min_stock' => 20,
                'status' => 'active',
            ],
            [
                'code' => 'OFIC-002',
                'name' => 'Lapiceros Azul',
                'description' => 'Lapiceros de tinta azul punta fina',
                'category_id' => 4,
                'unit_id' => 8,
                'current_stock' => 0,
                'min_stock' => 30,
                'status' => 'active',
            ],
            [
                'code' => 'OFIC-003',
                'name' => 'Archivador de Palanca',
                'description' => 'Archivador de palanca tamaño oficio lomo ancho',
                'category_id' => 4,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 15,
                'status' => 'active',
            ],

            // Ferretería
            [
                'code' => 'FERR-001',
                'name' => 'Martillo de Carpintero',
                'description' => 'Martillo con mango de madera y cabeza de acero',
                'category_id' => 5,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 5,
                'status' => 'active',
            ],
            [
                'code' => 'FERR-002',
                'name' => 'Clavos de 2 Pulgadas',
                'description' => 'Clavos de acero galvanizado de 2 pulgadas',
                'category_id' => 5,
                'unit_id' => 2,
                'current_stock' => 0,
                'min_stock' => 10,
                'status' => 'active',
            ],
            [
                'code' => 'FERR-003',
                'name' => 'Pintura Látex Blanca',
                'description' => 'Pintura látex interior/exterior color blanco',
                'category_id' => 5,
                'unit_id' => 4,
                'current_stock' => 0,
                'min_stock' => 8,
                'status' => 'active',
            ],

            // Textiles
            [
                'code' => 'TEXT-001',
                'name' => 'Camiseta Algodón Blanca',
                'description' => 'Camiseta 100% algodón color blanco talla M',
                'category_id' => 6,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 20,
                'status' => 'active',
            ],
            [
                'code' => 'TEXT-002',
                'name' => 'Toalla de Baño',
                'description' => 'Toalla de baño 100% algodón 70x140cm',
                'category_id' => 6,
                'unit_id' => 1,
                'current_stock' => 0,
                'min_stock' => 15,
                'status' => 'active',
            ],
            [
                'code' => 'TEXT-003',
                'name' => 'Medias Deportivas',
                'description' => 'Medias deportivas de algodón - Pack 3 pares',
                'category_id' => 6,
                'unit_id' => 9,
                'current_stock' => 0,
                'min_stock' => 25,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
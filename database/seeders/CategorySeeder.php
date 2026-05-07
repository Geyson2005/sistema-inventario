<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electrónica',
                'description' => 'Dispositivos electrónicos, accesorios y componentes',
                'status' => 'active',
            ],
            [
                'name' => 'Alimentos',
                'description' => 'Productos alimenticios perecederos y no perecederos',
                'status' => 'active',
            ],
            [
                'name' => 'Limpieza',
                'description' => 'Productos de limpieza, desinfección e higiene',
                'status' => 'active',
            ],
            [
                'name' => 'Oficina',
                'description' => 'Útiles escolares, papelería y materiales de oficina',
                'status' => 'active',
            ],
            [
                'name' => 'Ferretería',
                'description' => 'Herramientas, materiales de construcción y ferretería',
                'status' => 'active',
            ],
            [
                'name' => 'Textiles',
                'description' => 'Ropa, telas y productos textiles',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

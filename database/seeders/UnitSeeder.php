<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unidad', 'abbreviation' => 'UN'],
            ['name' => 'Kilogramo', 'abbreviation' => 'KG'],
            ['name' => 'Gramo', 'abbreviation' => 'GR'],
            ['name' => 'Litro', 'abbreviation' => 'LT'],
            ['name' => 'Mililitro', 'abbreviation' => 'ML'],
            ['name' => 'Metro', 'abbreviation' => 'MT'],
            ['name' => 'Centímetro', 'abbreviation' => 'CM'],
            ['name' => 'Caja', 'abbreviation' => 'CJ'],
            ['name' => 'Paquete', 'abbreviation' => 'PQ'],
            ['name' => 'Bolsa', 'abbreviation' => 'BS'],
            ['name' => 'Docena', 'abbreviation' => 'DOC'],
            ['name' => 'Par', 'abbreviation' => 'PAR'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
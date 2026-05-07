<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Distribuidora Tech SAC',
                'ruc' => '20123456789',
                'contact_person' => 'Juan Pérez García',
                'phone' => '987654321',
                'email' => 'ventas@distritech.com',
                'address' => 'Av. Industrial 123, Lima',
                'status' => 'active',
            ],
            [
                'name' => 'Alimentos del Norte EIRL',
                'ruc' => '20987654321',
                'contact_person' => 'María González López',
                'phone' => '976543210',
                'email' => 'contacto@alimentosnorte.com',
                'address' => 'Jr. Comercio 456, Piura',
                'status' => 'active',
            ],
            [
                'name' => 'Ferretería Central',
                'ruc' => '20456789123',
                'contact_person' => 'Carlos Ramírez Torres',
                'phone' => '965432109',
                'email' => 'info@ferreteriacentral.com',
                'address' => 'Av. Grau 789, Sullana',
                'status' => 'active',
            ],
            [
                'name' => 'Importaciones Global SAC',
                'ruc' => '20741852963',
                'contact_person' => 'Ana Martínez Ruiz',
                'phone' => '954321098',
                'email' => 'ventas@importglobal.com',
                'address' => 'Av. Argentina 321, Lima',
                'status' => 'active',
            ],
            [
                'name' => 'Proveedor Local',
                'ruc' => null,
                'contact_person' => 'Pedro Sánchez',
                'phone' => '943210987',
                'email' => 'pedrosanchez@gmail.com',
                'address' => 'Jr. Los Pinos 159',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
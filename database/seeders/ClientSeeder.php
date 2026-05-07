<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'code' => 'CLI-001',
                'name' => 'Juan Pérez García',
                'document_type' => 'DNI',
                'document_number' => '12345678',
                'phone' => '987654321',
                'email' => 'juan.perez@email.com',
                'address' => 'Av. Los Pinos 123, Trujillo',
                'status' => 'active',
            ],
            [
                'code' => 'CLI-002',
                'name' => 'Empresa ABC SAC',
                'document_type' => 'RUC',
                'document_number' => '20123456789',
                'phone' => '044123456',
                'email' => 'ventas@empresaabc.com',
                'address' => 'Jr. Comercio 456, Trujillo',
                'status' => 'active',
            ],
            [
                'code' => 'CLI-003',
                'name' => 'María López Torres',
                'document_type' => 'DNI',
                'document_number' => '87654321',
                'phone' => '976543210',
                'email' => 'maria.lopez@email.com',
                'address' => 'Calle Las Flores 789, La Libertad',
                'status' => 'active',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
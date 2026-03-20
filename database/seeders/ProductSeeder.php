<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'IT-001',
                'name' => 'Casco Integral Z1',
                'description' => 'Casco integral con certificación DOT, ventilación y mica anti-rayas.',
                'price' => 1250.00,
                'stock' => 20
            ],
            [
                'sku' => 'IT-002',
                'name' => 'Guantes Racing',
                'description' => 'Guantes de cuero reforzado con protecciones en nudillos.',
                'price' => 850.00,
                'stock' => 35
            ],
            [
                'sku' => 'IT-003',
                'name' => 'Aceite Sintético 10W40',
                'description' => 'Aceite 4T sintético para alto desempeño.',
                'price' => 320.00,
                'stock' => 50
            ],
        ];

        foreach ($products as $p) {
            // inserta o actualiza por SKU
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }
    }
}

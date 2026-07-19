<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

/**
 * Datos de referencia para probar el PromoCodeEngine vía HTTP (colección de Postman).
 *
 * El motor no expone endpoints para crear compradores, categorías u órdenes
 * (RF-08: la persistencia de esas entidades se documenta a nivel de diseño),
 * por lo que este seeder deja listo el mínimo indispensable para poder
 * ejercitar los endpoints de promo-codes de principio a fin.
 */
class PromoraDemoSeeder extends Seeder
{
    public function run(): void
    {
        $category = ServiceCategory::firstOrCreate(
            ['name' => 'Diseño Gráfico'],
        );

        ServiceCategory::firstOrCreate(
            ['name' => 'Diseño de Logos'],
            ['parent_id' => $category->id],
        );

        $newBuyer = Buyer::firstOrCreate(
            ['email' => 'ana.nueva@promora.test'],
            ['name' => 'Ana Cliente Nueva'],
        );

        $frequentBuyer = Buyer::firstOrCreate(
            ['email' => 'carlos.frecuente@promora.test'],
            ['name' => 'Carlos Cliente Frecuente'],
        );

        if ($frequentBuyer->orders()->where('order_status', 'completed')->count() < 3) {
            Order::factory()->count(3)->completed()->create([
                'buyer_id' => $frequentBuyer->id,
                'category_id' => $category->id,
            ]);
        }

        $this->command?->info("Categoría demo: {$category->id} ({$category->name})");
        $this->command?->info("Comprador nuevo (0 órdenes): {$newBuyer->id} ({$newBuyer->email})");
        $this->command?->info("Comprador frecuente (3 órdenes completadas): {$frequentBuyer->id} ({$frequentBuyer->email})");
    }
}

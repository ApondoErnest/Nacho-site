<?php

namespace Database\Factories;

use App\Models\Tariff;
use App\Models\TariffAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TariffAuditLog>
 */
class TariffAuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tariff_id' => Tariff::factory(),
            'user_id' => User::factory(),
            'changes' => [
                'price_fcfa' => [
                    'old' => 10000,
                    'new' => 12000,
                ],
            ],
        ];
    }
}

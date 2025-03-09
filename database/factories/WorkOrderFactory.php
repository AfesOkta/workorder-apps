<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrders>
 */
class WorkOrdersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'work_order_number' => 'WO-'.date('ymd') . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'product_name' => fake()->name(), // Gunakan $this->faker di sini
            'quantity' => $this->faker->numberBetween(1, 100),
            'deadline' => fake()->dateTimeBetween('now', '+1 year'), // Gunakan $this->faker di sini
            'status' => fake()->randomElement(['Pending', 'In Progress', 'Completed', 'Canceled']),
            'assigned_operator_id' => 1,
        ];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class WorkOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return [
            'work_order_number' => 'WO-'.date('ymd') . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'product_name' => $this->faker->name(), // Gunakan $this->faker di sini
            'quantity' => $this->faker->numberBetween(1, 100),
            'deadline' => $this->faker->dateTimeBetween('now', '+1 year'), // Gunakan $this->faker di sini
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Completed', 'Canceled']),
            'assigned_operator_id' => 1,
        ];
    }
}

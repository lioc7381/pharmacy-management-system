<?php

namespace Database\Seeders;

use App\Models\Medication;
use Illuminate\Database\Seeder;

/**
 * Seeder for creating sample medications.
 *
 * This seeder uses the MedicationFactory to generate a set of
 * sample medications for testing and demonstration purposes.
 */
class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Medication::factory()->count(15)->create();
    }
}

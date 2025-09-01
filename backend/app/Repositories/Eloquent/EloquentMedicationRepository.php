<?php

namespace App\Repositories\Eloquent;

use App\Models\Medication;
use App\Repositories\Contracts\MedicationRepositoryInterface;

/**
 * Eloquent implementation of the medication repository.
 *
 * This repository handles all database operations for medications
 * using Laravel's Eloquent ORM.
 */
class EloquentMedicationRepository implements MedicationRepositoryInterface
{
    /**
     * Get all active medications.
     *
     * @return array The active medications
     */
    public function getAllActive(): array
    {
        return Medication::active()->get()->toArray();
    }

    /**
     * Search medications by name using a case-insensitive LIKE query.
     *
     * @param string $name The name to search for
     * @return array The medications matching the search criteria
     */
    public function searchByName(string $name): array
    {
        return Medication::where('name', 'LIKE', '%' . $name . '%')
            ->active()
            ->get()
            ->toArray();
    }
}
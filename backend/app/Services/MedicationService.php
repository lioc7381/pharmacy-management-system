<?php

namespace App\Services;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service class handling all medication-related business logic.
 * 
 * This service encapsulates medication search operations.
 * It directly interacts with Eloquent models as mandated by the
 * architectural standards, avoiding the Repository pattern.
 */
class MedicationService
{
    /**
     * Searches for medications by name or retrieves all active medications.
     * 
     * @param string|null $name The name to search for.
     * @return Collection The collection of matching medications.
     */
    public function search(?string $name): Collection
    {
        $query = Medication::query();
        
        if (!empty($name)) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        } else {
            $query->where('status', 'active');
        }
        
        return $query->get();
    }
}
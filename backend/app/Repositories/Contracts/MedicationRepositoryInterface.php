<?php

namespace App\Repositories\Contracts;

/**
 * Interface for medication data access.
 *
 * This interface defines the contract for all medication data operations,
 * enabling dependency inversion and making the service layer testable.
 */
interface MedicationRepositoryInterface
{
    /**
     * Get all active medications.
     *
     * @return array The active medications
     */
    public function getAllActive(): array;

    /**
     * Search medications by name using a case-insensitive LIKE query.
     *
     * @param string $name The name to search for
     * @return array The medications matching the search criteria
     */
    public function searchByName(string $name): array;
}
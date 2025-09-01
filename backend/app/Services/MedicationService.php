<?php

namespace App\Services;

use App\Repositories\Contracts\MedicationRepositoryInterface;

/**
 * Service for handling medication business logic.
 *
 * This service encapsulates all business rules related to medications,
 * including search operations and manager-specific operations.
 */
class MedicationService
{
    /**
     * @var MedicationRepositoryInterface The repository for medication data access
     */
    protected $medicationRepository;

    /**
     * Create a new service instance.
     *
     * @param MedicationRepositoryInterface $medicationRepository
     * @return void
     */
    public function __construct(MedicationRepositoryInterface $medicationRepository)
    {
        $this->medicationRepository = $medicationRepository;
    }

    /**
     * Search for medications by name or retrieve all active medications.
     *
     * @param string|null $name The name to search for
     * @return array The medications matching the search criteria
     */
    public function search(?string $name): array
    {
        if (empty($name)) {
            return $this->medicationRepository->getAllActive();
        }
        
        return $this->medicationRepository->searchByName($name);
    }
}
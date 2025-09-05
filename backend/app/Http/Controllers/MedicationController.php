<?php

namespace App\Http\Controllers;

use App\Http\Requests\Medications\SearchMedicationRequest;
use App\Http\Resources\MedicationResource;
use App\Services\MedicationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Handles HTTP requests for medication management operations.
 * 
 * This controller manages medication-related endpoints including search,
 * creation, updates, and low-stock reporting. All business logic is 
 * delegated to the MedicationService class following the architectural mandate.
 */
class MedicationController extends Controller
{
    /**
     * The medication service instance for handling business logic.
     */
    private MedicationService $medicationService;

    /**
     * Creates a new MedicationController instance.
     * 
     * @param MedicationService $medicationService The service handling medication business logic.
     */
    public function __construct(MedicationService $medicationService)
    {
        $this->medicationService = $medicationService;
    }

    /**
     * Searches for medications by name or retrieves all active medications.
     * 
     * This is a public endpoint that allows searching the medication catalog.
     * If a name parameter is provided, it performs a case-insensitive LIKE search.
     * If no name parameter is provided, it returns all medications with status 'active'.
     * 
     * @param SearchMedicationRequest $request The validated request containing search parameters.
     * @return AnonymousResourceCollection The collection of matching medications.
     */
    public function index(SearchMedicationRequest $request): AnonymousResourceCollection
    {
        $name = $request->query('name');
        $medications = $this->medicationService->search($name);
        
        return MedicationResource::collection($medications);
    }
}
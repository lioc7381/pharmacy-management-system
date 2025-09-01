<?php

namespace App\Http\Controllers;

use App\Services\MedicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for handling medication-related API requests.
 *
 * This controller manages all operations related to medications,
 * including public search functionality and manager-specific
 * create/update/delete operations.
 */
class MedicationController extends Controller
{
    /**
     * @var MedicationService The service handling medication business logic
     */
    protected $medicationService;

    /**
     * Create a new controller instance.
     *
     * @param MedicationService $medicationService
     * @return void
     */
    public function __construct(MedicationService $medicationService)
    {
        $this->medicationService = $medicationService;
    }

    /**
     * Display a listing of medications.
     *
     * This is a public endpoint that allows searching for medications
     * by name or retrieving all active medications.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $name = $request->query('name');
        $medications = $this->medicationService->search($name);
        
        return response()->json($medications);
    }
}

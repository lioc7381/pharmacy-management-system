<?php

namespace Tests\Feature\Feature\Medication;

use App\Models\Medication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test suite for the Medication search API endpoint.
 * 
 * Covers searching for medications by name and retrieving all active medications.
 */
class SearchMedicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_user_can_search_medications_by_name(): void
    {
        // Arrange
        $medication1 = Medication::factory()->create([
            'name' => 'Paracetamol',
            'status' => 'active'
        ]);
        
        $medication2 = Medication::factory()->create([
            'name' => 'Ibuprofen',
            'status' => 'active'
        ]);
        
        $medication3 = Medication::factory()->create([
            'name' => 'Aspirin',
            'status' => 'active'
        ]);

        // Act
        $response = $this->getJson('/api/medications?name=para');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $medication1->id,
            'name' => 'Paracetamol'
        ]);
    }

    /** @test */
    public function public_user_gets_all_active_medications_when_no_name_provided(): void
    {
        // Arrange
        $activeMedication = Medication::factory()->create([
            'name' => 'Paracetamol',
            'status' => 'active'
        ]);
        
        $disabledMedication = Medication::factory()->create([
            'name' => 'Ibuprofen',
            'status' => 'disabled'
        ]);

        // Act
        $response = $this->getJson('/api/medications');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $activeMedication->id,
            'name' => 'Paracetamol'
        ]);
        $response->assertJsonMissing([
            'name' => 'Ibuprofen'
        ]);
    }

    /** @test */
    public function search_returns_empty_array_when_no_medications_match(): void
    {
        // Arrange
        Medication::factory()->create([
            'name' => 'Paracetamol',
            'status' => 'active'
        ]);

        // Act
        $response = $this->getJson('/api/medications?name=nonexistent');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
        $response->assertExactJson(['data' => []]);
    }
}
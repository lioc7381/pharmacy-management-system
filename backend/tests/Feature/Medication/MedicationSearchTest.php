<?php

namespace Tests\Feature\Medication;

use App\Models\Medication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test for the medication search endpoint.
 *
 * This test validates the public medication search functionality,
 * ensuring it works correctly with various search terms and edge cases.
 */
class MedicationSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test searching for medications by name.
     *
     * @return void
     */
    public function test_can_search_medications_by_name()
    {
        // Create test medications
        Medication::factory()->create([
            'name' => 'Paracetamol',
            'strength_form' => '500mg Tablet',
            'description' => 'For relief of mild to moderate pain.',
            'price' => 15.99,
            'current_quantity' => 250,
            'minimum_threshold' => 50,
            'category' => 'Pain Relief',
            'status' => 'active'
        ]);

        Medication::factory()->create([
            'name' => 'Ibuprofen',
            'strength_form' => '200mg Tablet',
            'description' => 'Non-steroidal anti-inflammatory drug.',
            'price' => 12.50,
            'current_quantity' => 150,
            'minimum_threshold' => 30,
            'category' => 'Pain Relief',
            'status' => 'active'
        ]);

        // Test case-insensitive search
        $response = $this->getJson('/api/medications?name=para');
        
        $response->assertStatus(200)
                 ->assertJsonCount(1)
                 ->assertJsonFragment(['name' => 'Paracetamol']);
    }

    /**
     * Test retrieving all active medications when no search term is provided.
     *
     * @return void
     */
    public function test_can_get_all_active_medications_when_no_search_term()
    {
        // Create test medications
        Medication::factory()->create([
            'name' => 'Paracetamol',
            'status' => 'active'
        ]);

        Medication::factory()->create([
            'name' => 'Ibuprofen',
            'status' => 'active'
        ]);

        Medication::factory()->create([
            'name' => 'Aspirin',
            'status' => 'disabled'
        ]);

        // Test getting all active medications
        $response = $this->getJson('/api/medications');
        
        $response->assertStatus(200)
                 ->assertJsonCount(2) // Only active medications
                 ->assertJsonFragment(['name' => 'Paracetamol'])
                 ->assertJsonFragment(['name' => 'Ibuprofen'])
                 ->assertJsonMissing(['name' => 'Aspirin']);
    }

    /**
     * Test that search returns an empty array when no medications match.
     *
     * @return void
     */
    public function test_search_returns_empty_array_when_no_medications_match()
    {
        Medication::factory()->create([
            'name' => 'Paracetamol',
            'status' => 'active'
        ]);

        // Search for a non-existent medication
        $response = $this->getJson('/api/medications?name=nonexistent');
        
        $response->assertStatus(200)
                 ->assertJsonCount(0);
    }
}
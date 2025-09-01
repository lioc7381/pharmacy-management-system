<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Medication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test to verify that the database seeding works correctly.
 *
 * This test ensures that after running the seeders, the database
 * contains the expected number of users and medications.
 */
class DatabaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the database is seeded with the correct number of users and medications.
     *
     * @return void
     */
    public function test_database_is_seeded_correctly()
    {
        // Run the seeders
        $this->artisan('db:seed');

        // Assert that we have the correct number of users
        $this->assertEquals(5, User::count());

        // Assert that we have the correct number of medications
        $this->assertEquals(15, Medication::count());

        // Assert that we have users with all required roles
        $this->assertDatabaseHas('users', ['role' => 'client']);
        $this->assertDatabaseHas('users', ['role' => 'pharmacist']);
        $this->assertDatabaseHas('users', ['role' => 'salesperson']);
        $this->assertDatabaseHas('users', ['role' => 'delivery']);
        $this->assertDatabaseHas('users', ['role' => 'manager']);
    }
}
